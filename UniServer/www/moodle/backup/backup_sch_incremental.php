<?php //$Id$
    //This file contains all the code needed to execute scheduled backups

//This function is executed via moodle cron
//It prepares all the info and execute backups as necessary

function schedule_incr_backup_cron() {

    global $CFG;

    $status = true;

    $emailpending = false;

    //Check for required functions...
    if(!function_exists('utf8_encode')) {
        mtrace("        ERROR: You need to add XML support to your PHP installation!");
        return true;
    }

    //Get now
    $now = time();

    //First of all, we have to see if the scheduled is active and detect
    //that there isn't another cron running
    mtrace("    Checking incremental status",'...');
    $incremental_config = backup_get_config();
    
    $xdeltapath = $incremental_config->backup_inc_pathtoxdelta;
    //check for relative path first.
    if (!empty($xdeltapath) && strpos($xdeltapath, '..') !==false) { 
        //this is a relative path - convert it before using it!
        $xdeltapath = realpath($CFG->dataroot.$xdeltapath);
    }
    if(empty($xdeltapath) or !file_exists($xdeltapath)) {
        mtrace("  XDELTA not installed - incrementals cannot run");
        return false;
    }
    if(!isset($incremental_config->backup_sche_incrementals) || !$incremental_config->backup_sche_incrementals) {
        mtrace("INACTIVE");
        return true;
    } else if (isset($incremental_config->backup_inc_running) && $incremental_config->backup_inc_running) {
        mtrace("RUNNING");
        //Now check if it's a really running task or something very old looking
        //for info in backup_logs to unlock status as necessary
        $timetosee = 1800;   //Half an hour looking for activity
        $timeafter = time() - $timetosee;
        //TODO DAN - this needs to check the inc log to see if it is still going.
        $numofrec = count_records_select ("backup_log","time > $timeafter AND type='incrementalbackup'");
        if (!$numofrec) {
            $timetoseemin = $timetosee/60;
            mtrace("    No activity in last ".$timetoseemin." minutes. Unlocking status");
        } else {
            mtrace("    Scheduled backup seems to be running. Execution delayed");
            return true;
        }
    } else {
        mtrace("OK");
        //Mark backup_inc_running
        backup_set_config("backup_inc_running","1");
    }

    //Now we get the main admin user (we'll use his timezone, mail...)
    mtrace("    Getting admin info");
    $admin = get_admin();
    if (!$admin) {
        $status = false;
    }

    //Now we get a list of courses in the server
    if ($status) {
        mtrace("    Checking courses");
        
        //now pull courses out of incremental_courses
        $bkcourses = get_records('incremental_courses');

        //For each course, we check (insert, update) the backup_course table
        //with needed data
        if (empty($bkcourses)) {
            mtrace("    No courses configured for incremental backups");
        } else {
            foreach ($bkcourses as $backup_course) {
                //TODO - this should be tidied up to use a single sql query instead of 1 per course configured!
                $course = get_record('course', 'id', $backup_course->courseid);
                if ($status) {
                    mtrace("        $course->fullname");
                    
                    //Now we backup every non skipped course with nextstarttime < now
                    if ($backup_course->nextstarttime > 0 && $backup_course->nextstarttime < $now) {
                        //We have to send a email because we have included at least one backup
                        $emailpending = true;
                        //Only make the backup if laststatus isn't 2-UNFINISHED (uncontrolled error)
                        if ($backup_course->laststatus != 2) {
                            //Set laststarttime
                            $starttime = time();
                            set_field("incremental_courses","laststarttime",$starttime,"courseid",$backup_course->courseid);
                            //Set course status to unfinished, the process will reset it
                            set_field("incremental_courses","laststatus","2","courseid",$backup_course->courseid);
                            //Launch backup
                            $course_status = schedule_backup_launch_inc_backup($course,$starttime);
                            //Set lastendtime
                            set_field("incremental_courses","lastendtime",time(),"courseid",$backup_course->courseid);
                            //Set laststatus
                            if ($course_status) {
                                set_field("incremental_courses","laststatus","1","courseid",$backup_course->courseid);
                            } else {
                                set_field("incremental_courses","laststatus","0","courseid",$backup_course->courseid);
                            }
                        }
                    }

                    //Now, calculate next execution of the course
                    $nextstarttime = schedule_inc_backup_next_execution ($backup_course,$incremental_config,$now,$admin->timezone);
                    //Save it to db
                    set_field("incremental_courses","nextstarttime",$nextstarttime,"courseid",$backup_course->courseid);
                    //Print it to screen as necessary
                    $showtime = "undefined";
                    if ($nextstarttime > 0) {
                        $showtime = userdate($nextstarttime,"",$admin->timezone);
                    }
                    mtrace("            Next execution: $showtime");
                }
            }
        }
    }

    //Delete old logs
    if (!empty($CFG->loglifetime)) {
        mtrace("    Deleting old logs");
        $loglifetime = $now - ($CFG->loglifetime * 86400);
        delete_records_select("incremental_log", "laststarttime < '$loglifetime'");
    }

    //Send email to admin if necessary
    if ($emailpending) {
        mtrace("    Sending email to admin");
        $message = "";

        //Get info about the status of courses
        $count_all = count_records('incremental_courses');
        $count_ok = count_records('incremental_courses','laststatus','1');
        $count_error = count_records('incremental_courses','laststatus','0');
        $count_unfinished = count_records('incremental_courses','laststatus','2');
        $count_skipped = count_records('incremental_courses','laststatus','3');

        //Build the message text
        //Summary
        $message .= get_string('summary')."\n";
        $message .= "==================================================\n";
        $message .= "  ".get_string('courses').": ".$count_all."\n";
        $message .= "  ".get_string('ok').": ".$count_ok."\n";
        $message .= "  ".get_string('skipped').": ".$count_skipped."\n";
        $message .= "  ".get_string('error').": ".$count_error."\n";
        $message .= "  ".get_string('unfinished').": ".$count_unfinished."\n\n";

        //Reference
        if ($count_error != 0 || $count_unfinished != 0) {
            $message .= "  ".get_string('incrementalfailed')."\n\n";
            $dest_url = "$CFG->wwwroot/$CFG->admin/report/incrementals/index.php";
            $message .= "  ".get_string('incrementaltakealook','',$dest_url)."\n\n";
            //Set message priority
            $admin->priority = 1;
            //Reset unfinished to error
            set_field('incremental_courses','laststatus','0','laststatus','2');
        } else {
            $message .= "  ".get_string('incrementalfinished')."\n";
        }

        //Build the message subject
        $site = get_site();
        $prefix = $site->shortname.": ";
        if ($count_error != 0 || $count_unfinished != 0) {
            $prefix .= "[".strtoupper(get_string('error'))."] ";
        }
        $subject = $prefix.get_string("incrementalbackupstatus");

        //Send the message
        email_to_user($admin,$admin,$subject,$message);
    }


    //Everything is finished stop backup_sche_running
    backup_set_config("backup_inc_running","0");

    return $status;
}

//This function executes the ENTIRE backup of a course (passed as parameter)
//using all the scheduled backup preferences
function schedule_backup_launch_inc_backup($course,$starttime = 0, $preferences='') {
    global $CFG;
    $status = false;

    mtrace("            Executing backup");
    backup_add_to_log($starttime,$course->id,"Start backup course $course->fullname",'incrementalbackup');
    backup_add_to_log($starttime,$course->id,"  Phase 1: Checking and counting:",'incrementalbackup');
    if (empty($preferences)) {
        $preferences = schedule_inc_backup_course_configure($course,$starttime);
    }

    if ($preferences) {
        backup_add_to_log($starttime,$course->id,"  Phase 2: Executing and copying:",'incrementalbackup');
        $status = schedule_inc_backup_course_execute($preferences,$starttime);
    }

    if ($status && $preferences) {
        //Only if the backup_sche_keep is set
        if ($preferences->backup_keep) {
            backup_add_to_log($starttime,$course->id,"  Phase 3: Deleting old backup files:",'incrementalbackup');
            $status = schedule_inc_backup_course_delete_old_files($preferences,$starttime);
        }
    }
    if ($status && $preferences) {
        if ($preferences->backup_sche_incrementals) {
            mtrace("            Generating Incrementals");
            //generate incrementals for this course.
            require_once ("$CFG->libdir/xdelta.class.php");
            require_once ("incremental_backuplib.php");
            backup_add_to_log($starttime,$course->id,"  Phase 4: Generating Incrementals:",'incrementalbackup');
            $status = generate_incrementals($preferences->backup_course);;
        }
    }

    if ($status && $preferences) {
        mtrace("            End backup OK");
        backup_add_to_log($starttime,$course->id,"End backup course $course->fullname - OK",'incrementalbackup');
    } else {
        mtrace("            End backup with ERROR");
        backup_add_to_log($starttime,$course->id,"End backup course $course->fullname - ERROR!!",'incrementalbackup');
    }

    return $status && $preferences;
}


//This function returns the next future GMT time to execute the course based in the
//configuration of the scheduled backups
function schedule_inc_backup_next_execution ($backup_course,$incremental_config,$now,$timezone) {
    $result = -1;
    //Get today's midnight GMT
    $midnight = usergetmidnight($now,$timezone);

    //Get today's day of week (0=Sunday...6=Saturday)
    $date = usergetdate($now,$timezone);
    $dayofweek = $date['wday'];

    //Get number of days (from today) to execute backups
    $scheduled_days = substr($incremental_config->backup_inc_weekdays,$dayofweek).
                      $incremental_config->backup_inc_weekdays;
    $daysfromtoday = strpos($scheduled_days, "1");

    //If some day has been found
    if ($daysfromtoday !== false) {
        //Calculate distance
        $dist = ($daysfromtoday * 86400) +                     //Days distance
                ($incremental_config->backup_inc_hour*3600) +      //Hours distance
                ($incremental_config->backup_inc_minute*60);       //Minutes distance
        $result = $midnight + $dist;
    }
    //If that time is past, call the function recursively to obtain the next valid day
    if ($result > 0 && $result < time()) {
        $result = schedule_inc_backup_next_execution ($backup_course,$incremental_config,$now + 86400,$timezone);
    }

    return $result;
}



//This function implements all the needed code to prepare a course
//to be in backup (insert temp info into backup temp tables).
function schedule_inc_backup_course_configure($course,$starttime = 0) {

    global $CFG;

    $status = true;

    backup_add_to_log($starttime,$course->id,"    checking parameters",'incrementalbackup');

    //Check the required variable
    if (empty($course->id)) {
        $status = false;
    }
    //Get scheduled backup preferences
    $incremental_config =  backup_get_config();

    //Checks backup_config pairs exist
    if ($status) {
        if (!isset($incremental_config->backup_inc_modules)) {
            $incremental_config->backup_inc_modules = 1;
        }
        if (!isset($incremental_config->backup_inc_withuserdata)) {
            $incremental_config->backup_inc_withuserdata = 1;
        }
        if (!isset($incremental_config->backup_inc_metacourse)) {
            $incremental_config->backup_inc_metacourse = 1;
        }
        if (!isset($incremental_config->backup_inc_users)) {
            $incremental_config->backup_inc_users = 1;
        }
        if (!isset($incremental_config->backup_inc_logs)) {
            $incremental_config->backup_inc_logs = 0;
        }
        if (!isset($incremental_config->backup_inc_userfiles)) {
            $incremental_config->backup_inc_userfiles = 1;
        }
        if (!isset($incremental_config->backup_inc_coursefiles)) {
            $incremental_config->backup_inc_coursefiles = 1;
        }
        if (!isset($incremental_config->backup_inc_sitefiles)) {
            $incremental_config->backup_inc_sitefiles = 1;
        }
        if (!isset($incremental_config->backup_inc_messages)) {
            $incremental_config->backup_inc_messages = 0;
        }
        if (!isset($incremental_config->backup_inc_weekdays)) {
            $incremental_config->backup_inc_weekdays = "0000000";
        }
        if (!isset($incremental_config->backup_inc_hour)) {
            $incremental_config->backup_inc_hour = 00;
        }
        if (!isset($incremental_config->backup_inc_minute)) {
            $incremental_config->backup_inc_minute = 00;
        }
        if (!isset($incremental_config->backup_inc_destination)) {
            $incremental_config->backup_inc_destination = "";
        }
        if (!isset($incremental_config->backup_inc_keep)) {
            $incremental_config->backup_inc_keep = 1;
        }
        if (!isset($incremental_config->backup_sche_incrementals)) {
            $incremental_config->backup_sche_incrementals = 0;
        }
        if (!isset($incremental_config->backup_inc_privatedata)) {
            $incremental_config->backup_inc_privatedata = 0;
        }

    }

    if ($status) {
       //Checks for the required files/functions to backup every mod
        //And check if there is data about it
        
        //use the configured modules to backup.
        
        $count = 0;
        if (!empty($incremental_config->backup_inc_modules)) {
            $moduleselect = 'id='.str_replace(',', ' OR id=', $incremental_config->backup_inc_modules);
            if ($allmods = get_records_select("modules", $moduleselect) ) {
                foreach ($allmods as $mod) {
                    $modname = $mod->name;
                    $modfile = "$CFG->dirroot/mod/$modname/backuplib.php";
                    $modbackup = $modname."_backup_mods";
                    $modcheckbackup = $modname."_check_backup_mods";
                    if (file_exists($modfile)) {
                       include_once($modfile);
                       if (function_exists($modbackup) and function_exists($modcheckbackup)) {
                           $var = "exists_".$modname;
                           $$var = true;
                           $count++;

                           // PENNY NOTES: I have moved from here to the closing brace inside
                           // by two sets of ifs()
                           // to avoid the backup failing on a non existant backup.
                           // If the file/function/whatever doesn't exist, we don't want to set this
                           // this module in backup preferences at all.
                           //Check data
                           //Check module info
                           $var = "backup_".$modname;
                           if (!isset($$var)) {
                               $$var = $incremental_config->backup_inc_modules;
                           }
                           //Now stores all the mods preferences into an array into preferences
                           $preferences->mods[$modname]->backup = $$var;

                           //Check include user info
                           $var = "backup_user_info_".$modname;
                           if (!isset($$var)) {
                               $$var = $incremental_config->backup_inc_withuserdata;
                           }
                           //Now stores all the mods preferences into an array into preferences
                           $preferences->mods[$modname]->userinfo = $$var;
                           //And the name of the mod
                           $preferences->mods[$modname]->name = $modname;
                       }
                    }
                }
            }
        }
        // now set instances
        if ($coursemods = get_course_mods($course->id)) {
            foreach ($coursemods as $mod) {
                if (array_key_exists($mod->modname,$preferences->mods)) { // we are to backup this module
                    if (empty($preferences->mods[$mod->modname]->instances)) {
                        $preferences->mods[$mod->modname]->instances = array(); // avoid warnings
                    }
                    $preferences->mods[$mod->modname]->instances[$mod->instance]->backup = $preferences->mods[$mod->modname]->backup;
                    $preferences->mods[$mod->modname]->instances[$mod->instance]->userinfo = $preferences->mods[$mod->modname]->userinfo;
                    // there isn't really a nice way to do this...
                    $preferences->mods[$mod->modname]->instances[$mod->instance]->name = get_field($mod->modname,'name','id',$mod->instance);
                }
            }
        }

        // finally, clean all the $preferences->mods[] not having instances. Nothing to backup about them
        if (!empty($preferences->mods)) {
            foreach ($preferences->mods as $modname => $mod) {
                if (!isset($mod->instances)) {
                    unset($preferences->mods[$modname]);
                }
            }
        }
    }

    //Convert other parameters
    if ($status) {
        $preferences->backup_metacourse = $incremental_config->backup_inc_metacourse;
        $preferences->backup_users = $incremental_config->backup_inc_users;
        $preferences->backup_logs = $incremental_config->backup_inc_logs;
        $preferences->backup_user_files = $incremental_config->backup_inc_userfiles;
        $preferences->backup_course_files = $incremental_config->backup_inc_coursefiles;
        $preferences->backup_site_files = $incremental_config->backup_inc_sitefiles;
        $preferences->backup_messages = $incremental_config->backup_inc_messages;
        $preferences->backup_gradebook_history = 1;
        $preferences->backup_course = $course->id;
        $preferences->backup_private_user_data = $incremental_config->backup_inc_privatedata;
        if (!empty($incremental_config->backup_inc_destination)) {
            if (!check_dir_exists($incremental_config->backup_inc_destination.'/'.$course->id, true, true)){   
                error('failed to create backup destination directory');
            } else {
                $preferences->backup_destination = $incremental_config->backup_inc_destination.'/'.$course->id;
            }
        }
        $preferences->backup_keep = $incremental_config->backup_inc_keep;
        $preferences->backup_sche_incrementals = $incremental_config->backup_sche_incrementals;
    }

    //Calculate various backup preferences
    if ($status) {
        backup_add_to_log($starttime,$course->id,"    calculating backup name",'incrementalbackup');

        //Calculate the backup file name
        $backup_name = backup_get_zipfile_name($course);

        //Calculate the string to match the keep preference
        $keep_name = backup_get_keep_name($course);

        //Set them
        $preferences->backup_name = $backup_name;
        $preferences->keep_name = $keep_name;

        //Roleasignments
        $roles = get_records('role', '', '', 'sortorder');
        foreach ($roles as $role) {
            $preferences->backuproleassignments[$role->id] = $role;
        }

        //Another Info
        backup_add_static_preferences($preferences);
    }

    //Calculate the backup unique code to allow simultaneus backups (to define
    //the temp-directory name and records in backup temp tables
    if ($status) {
        $backup_unique_code = time();
        $preferences->backup_unique_code = $backup_unique_code;
    }

    //Calculate necesary info to backup modules
    if ($status) {
        backup_add_to_log($starttime,$course->id,"    calculating modules data",'incrementalbackup');
        if ($allmods = get_records("modules") ) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $modbackup = $modname."_backup_mods";
                //If exists the lib & function
                $var = "exists_".$modname;
                if (isset($$var) && $$var) {
                    //Add hidden fields
                    $var = "backup_".$modname;
                    //Only if selected
                    if ($$var == 1) {
                        $var = "backup_user_info_".$modname;
                        //Call the check function to show more info
                        $modcheckbackup = $modname."_check_backup_mods";
                        backup_add_to_log($starttime,$course->id,"      $modname",'incrementalbackup');
                        $modcheckbackup($course->id,$$var,$backup_unique_code);
                    }
                }
            }
        }
    }

    //Now calculate the users
    if ($status) {
        backup_add_to_log($starttime,$course->id,"    calculating users",'incrementalbackup');
        //Decide about include users with messages, based on SITEID
        if ($preferences->backup_messages && $preferences->backup_course == SITEID) {
            $include_message_users = true;
        } else {
            $include_message_users = false;
        }
        //Decide about include users with blogs, based on SITEID
        if (isset($preferences->backup_blogs) && isset($preferences->backup_course) && $preferences->backup_blogs && $preferences->backup_course == SITEID) {
            $include_blog_users = true;
        } else {
            $include_blog_users = false;
        }
        user_check_backup($course->id,$backup_unique_code,$preferences->backup_users,$include_message_users, $include_blog_users);
    }

    //Now calculate the logs
    if ($status) {
        if ($preferences->backup_logs) {
            backup_add_to_log($starttime,$course->id,"    calculating logs",'incrementalbackup');
            log_check_backup($course->id);
        }
    }

    //Now calculate the userfiles
    if ($status) {
        if ($preferences->backup_user_files) {
            backup_add_to_log($starttime,$course->id,"    calculating user files",'incrementalbackup');
            user_files_check_backup($course->id,$preferences->backup_unique_code);
        }
    }

    //Now calculate the coursefiles
    if ($status) {
       if ($preferences->backup_course_files) {
            backup_add_to_log($starttime,$course->id,"    calculating course files",'incrementalbackup');
            course_files_check_backup($course->id,$preferences->backup_unique_code);
        }
    }

    //Now calculate the sitefiles
    if ($status) {
       if ($preferences->backup_site_files) {
            backup_add_to_log($starttime,$course->id,"    calculating site files",'incrementalbackup');
            site_files_check_backup($course->id,$preferences->backup_unique_code);
        }
    }

    //If everything is ok, return calculated preferences
    if ($status) {
        $status = $preferences;
    }

    return $status;
}

//TODO: Unify this function with backup_execute() to have both backups 100% equivalent. Moodle 2.0

//This function implements all the needed code to backup a course
//copying it to the desired destination (default if not specified)
function schedule_inc_backup_course_execute($preferences,$starttime = 0) {

    global $CFG;

    $status = true;

    //Some parts of the backup doesn't know about $preferences, so we
    //put a copy of it inside that CFG (always global) to be able to
    //use it. Then, when needed I search for preferences inside CFG
    //Used to avoid some problems in full_tag() when preferences isn't
    //set globally (i.e. in scheduled backups)
    $CFG->backup_preferences = $preferences;

    //Check for temp and backup and backup_unique_code directory
    //Create them as needed
    backup_add_to_log($starttime,$preferences->backup_course,"    checking temp structures",'incrementalbackup');
    $status = check_and_create_backup_dir($preferences->backup_unique_code);
    //Empty backup dir
    if ($status) {
        backup_add_to_log($starttime,$preferences->backup_course,"    cleaning current dir",'incrementalbackup');
        $status = clear_backup_dir($preferences->backup_unique_code);
    }

    //Create the moodle.xml file
    if ($status) {
        backup_add_to_log($starttime,$preferences->backup_course,"    creating backup file",'incrementalbackup');
        //Obtain the xml file (create and open) and print prolog information
        $backup_file = backup_open_xml($preferences->backup_unique_code);
        //Prints general info about backup to file
        if ($backup_file) {
            backup_add_to_log($starttime,$preferences->backup_course,"      general info",'incrementalbackup');
            $status = backup_general_info($backup_file,$preferences);
        } else {
            $status = false;
        }

        //Prints course start (tag and general info)
        if ($status) {
            $status = backup_course_start($backup_file,$preferences);
        }

        //Metacourse information
        if ($status && $preferences->backup_metacourse) {
            backup_add_to_log($starttime,$preferences->backup_course,"      metacourse info",'incrementalbackup');
            $status = backup_course_metacourse($backup_file,$preferences);
        }

        //Block info
        if ($status) {
            backup_add_to_log($starttime,$preferences->backup_course,"      blocks info",'incrementalbackup');
            $status = backup_course_blocks($backup_file,$preferences);
        }

        //Section info
        if ($status) {
            backup_add_to_log($starttime,$preferences->backup_course,"      sections info",'incrementalbackup');
            $status = backup_course_sections($backup_file,$preferences);
        }

        //User info
        if ($status) {
            backup_add_to_log($starttime,$preferences->backup_course,"      user info",'incrementalbackup');
            $status = backup_user_info($backup_file,$preferences);
        }

        //If we have selected to backup messages and we are
        //doing a SITE backup, let's do it
        if ($status && $preferences->backup_messages && $preferences->backup_course == SITEID) {
            backup_add_to_log($starttime,$preferences->backup_course,"      messages",'incrementalbackup');
            if (!$status = backup_messages($backup_file,$preferences)) {
                notify("An error occurred while backing up messages");
            }
        }

        //If we have selected to backup blogs and we are
        //doing a SITE backup, let's do it
        if ($status && isset($preferences->backup_blogs) && isset($preferences->backup_course) && $preferences->backup_blogs && $preferences->backup_course == SITEID) {
            schedule_backup_log($starttime,$preferences->backup_course,"      blogs",'incrementalbackup');
            $status = backup_blogs($backup_file,$preferences);
        }

        //If we have selected to backup quizzes, backup categories and
        //questions structure (step 1). See notes on mod/quiz/backuplib.php
        if ($status and isset($preferences->mods['quiz']->backup)) {
            backup_add_to_log($starttime,$preferences->backup_course,"      categories & questions",'incrementalbackup');
            $status = backup_question_categories($backup_file,$preferences);
        }

        //Print logs if selected
        if ($status) {
            if ($preferences->backup_logs) {
                backup_add_to_log($starttime,$preferences->backup_course,"      logs",'incrementalbackup');
                $status = backup_log_info($backup_file,$preferences);
            }
        }

        //Print scales info
        if ($status) {
            backup_add_to_log($starttime,$preferences->backup_course,"      scales",'incrementalbackup');
            $status = backup_scales_info($backup_file,$preferences);
        }

        //Print groups info
        if ($status) {
            backup_add_to_log($starttime,$preferences->backup_course,"      groups",'incrementalbackup');
            $status = backup_groups_info($backup_file,$preferences);
        }

        //Print groupings info
        if ($status) {
            backup_add_to_log($starttime,$preferences->backup_course,"      groupings",'incrementalbackup');
            $status = backup_groupings_info($backup_file,$preferences);
        }

        //Print groupings_groups info
        if ($status) {
            backup_add_to_log($starttime,$preferences->backup_course,"      groupings_groups",'incrementalbackup');
            $status = backup_groupings_groups_info($backup_file,$preferences);
        }

        //Print events info
        if ($status) {
            backup_add_to_log($starttime,$preferences->backup_course,"      events",'incrementalbackup');
            $status = backup_events_info($backup_file,$preferences);
        }

        //Print gradebook info
        if ($status) {
            backup_add_to_log($starttime,$preferences->backup_course,"      gradebook",'incrementalbackup');
            $status = backup_gradebook_info($backup_file,$preferences);
        }

        //Module info, this unique function makes all the work!!
        //db export and module fileis copy
        if ($status) {
            $mods_to_backup = false;
            //Check if we have any mod to backup
            if (!empty($preferences->mods)) {
                foreach ($preferences->mods as $module) {
                    if ($module->backup) {
                        $mods_to_backup = true;
                    }
                }
            }
            //If we have to backup some module
            if ($mods_to_backup) {
                backup_add_to_log($starttime,$preferences->backup_course,"      modules",'incrementalbackup');
                //Start modules tag
                $status = backup_modules_start ($backup_file,$preferences);
                //Iterate over modules and call backup
                foreach ($preferences->mods as $module) {
                    if ($module->backup and $status) {
                        backup_add_to_log($starttime,$preferences->backup_course,"        $module->name",'incrementalbackup');
                        $status = backup_module($backup_file,$preferences,$module->name);
                    }
                }
                //Close modules tag
                $status = backup_modules_end ($backup_file,$preferences);
            }
        }

        //Backup course format data, if any.
        if ($status) {
            backup_add_to_log($starttime,$preferences->backup_course,"      course format data",'incrementalbackup');
            $status = backup_format_data($backup_file,$preferences);
        }

        //Prints course end
        if ($status) {
            $status = backup_course_end($backup_file,$preferences);
        }

        //Close the xml file and xml data
        if ($backup_file) {
            backup_close_xml($backup_file);
        }
    }

    //Now, if selected, copy user files
    if ($status) {
        if ($preferences->backup_user_files) {
            backup_add_to_log($starttime,$preferences->backup_course,"    copying user files",'incrementalbackup');
            $status = backup_copy_user_files ($preferences);
        }
    }

    //Now, if selected, copy course files
    if ($status) {
        if ($preferences->backup_course_files) {
            backup_add_to_log($starttime,$preferences->backup_course,"    copying course files",'incrementalbackup');
            $status = backup_copy_course_files ($preferences);
        }
    }

    //Now, if selected, copy site files
    if ($status) {
        if ($preferences->backup_site_files) {
            backup_add_to_log($starttime,$preferences->backup_course,"    copying site files",'incrementalbackup');
            $status = backup_copy_site_files ($preferences);
        }
    }

    //Now, zip all the backup directory contents
    if ($status) {
        backup_add_to_log($starttime,$preferences->backup_course,"    zipping files",'incrementalbackup');
        $status = backup_zip ($preferences);
    }

    //Now, copy the zip file to course directory
    if ($status) {
        backup_add_to_log($starttime,$preferences->backup_course,"    copying backup",'incrementalbackup');
        $status = copy_zip_to_course_dir ($preferences);
    }

    //Now, clean temporary data (db and filesystem)
    if ($status) {
        backup_add_to_log($starttime,$preferences->backup_course,"    cleaning temp data",'incrementalbackup');
        $status = clean_temp_data ($preferences);
    }

    //Unset CFG->backup_preferences only needed in scheduled backups
    unset ($CFG->backup_preferences);

    return $status;
}

//This function deletes old backup files when the "keep" limit has been reached
//in the destination directory.
function schedule_inc_backup_course_delete_old_files($preferences,$starttime=0) {

    global $CFG;

    $status = true;

    //Calculate the directory to check
    $dirtocheck = "";
    //if $preferences->backup_destination isn't empty, then check that directory
    if (!empty($preferences->backup_destination)) {
        $dirtocheck = $preferences->backup_destination;
    //else calculate standard backup directory location
    } else {
        $dirtocheck = $CFG->dataroot."/".$preferences->backup_course."/backupdata";
    }
    backup_add_to_log($starttime,$preferences->backup_course,"    checking $dirtocheck",'incrementalbackup');
    if ($CFG->debug > 7) {
        mtrace("            Keeping backup files in $dirtocheck");
    }

    //Get all the files in $dirtocheck
    $files = get_directory_list($dirtocheck,"",false);
    //Get all matching files ($preferences->keep_name) from $files
    $matchingfiles = array();
    foreach ($files as $file) {
        if (substr($file, 0, strlen($preferences->keep_name)) == $preferences->keep_name) {
            $modifieddate = filemtime($dirtocheck."/".$file);
            $matchingfiles[$modifieddate] = $file;
        }
    }
    //Sort by key (modified date) to get the oldest first (instead of doing that by name
    //because it could give us problems in some languages with different format names).
    ksort($matchingfiles);

    //Count matching files
    $countmatching = count($matchingfiles);
    backup_add_to_log($starttime,$preferences->backup_course,"        found $countmatching backup files",'incrementalbackup');
    mtrace("                found $countmatching backup files");
    if ($preferences->backup_keep < $countmatching) {
        backup_add_to_log($starttime,$preferences->backup_course,"        keep limit ($preferences->backup_keep) reached. Deleting old files",'incrementalbackup');
        mtrace("                keep limit ($preferences->backup_keep) reached. Deleting old files");
        $filestodelete = $countmatching - $preferences->backup_keep;
        $filesdeleted = 0;
        foreach ($matchingfiles as $matchfile) {
            if ($filesdeleted < $filestodelete) {
                backup_add_to_log($starttime,$preferences->backup_course,"        $matchfile deleted",'incrementalbackup');
                mtrace("                $matchfile deleted");
                $filetodelete = $dirtocheck."/".$matchfile;
                unlink($filetodelete);
                $filesdeleted++;
            }
        }
    }
    return $status;
}

?>
