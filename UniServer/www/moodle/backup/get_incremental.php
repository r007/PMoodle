<?php
/*
 * This file is called by the offline Moodle client and sends the existing
 * incremental or Full backup if the incremental no longer exists.
 * 
 */    
 
 require('../config.php');
 require_once("$CFG->dirroot/backup/backup_sch_incremental.php");
 require_once("$CFG->dirroot/backup/incremental_backuplib.php");
 require_once("$CFG->dirroot/backup/backuplib.php");
 require_once("$CFG->dirroot/backup/lib.php");
 require_once("$CFG->dirroot/lib/filelib.php");
 require_once("$CFG->dirroot/lib/xdelta.class.php");
  
 require_once("../lib/xmlize.php");
 require_once("../course/lib.php");
 require_once("restorelib.php");
 require_once("bb/restore_bb.php");
 require_once("$CFG->libdir/blocklib.php");
 require_once("$CFG->libdir/wiki_to_markdown.php" );
 require_once("$CFG->libdir/adminlib.php");
 
 $action = optional_param('action', '', PARAM_ALPHA);
 if ($action <> 'newcourse') {    
     $id = required_param('id',PARAM_INT);   // course
      if (! $course = get_record("course", "id", $id)) {
         error("Course ID is incorrect");
      }
 } else {
    //need to create a new course.
       // set defaults
        $course = new object();
        $course->student  = get_string('defaultcoursestudent');
        $course->students = get_string('defaultcoursestudents');
        $course->teacher  = get_string('defaultcourseteacher');
        $course->teachers = get_string('defaultcourseteachers');
        $course->format = 'topics';
        $course->idnumber  = 'offline';
        $course->fullname  = 'Offline Moodle';
        $course->shortname = 'Offline';
        $course->summary = '';
        $course->category = 1;
        $i = 1;
        while ($existingcourse = get_record('course', 'shortname', $course->shortname)) {       
            $course->shortname .='_'.$i;
            $course->idnumber  .='_'.$i;
            $course->fullname  .='_'.$i;
            $i++;
        }
        // define the sortorder (yuck)
        $sort = get_record_sql('SELECT MAX(sortorder) AS max, 1 FROM ' . $CFG->prefix . 'course WHERE category=' . $course->category);
        $sort = $sort->max;
        $sort++;
        $course->sortorder = $sort; 
        
        // override with local data
        $course->startdate = time();
        $course->timecreated = time();
        $course->visible     = 1;
    
        $course = addslashes_recursive($course);
        
        if ($newcourseid = insert_record("course", $course)) {  // Set up new course
           $section = new object();
           $section->course = $newcourseid;   // Create a default section.
           $section->section = 0;
           $section->id = insert_record("course_sections", $section);
           $page = page_create_object(PAGE_COURSE_VIEW, $newcourseid);

           blocks_repopulate_page($page); // Return value no
           
           $blockclient = get_field('block', 'id', 'name', 'incrementalclient');

            $newinstance = new stdClass;
            $newinstance->blockid    = $blockclient;
            $newinstance->pageid     = $page->get_id();
            $newinstance->pagetype   = $page->get_type();
            $newinstance->position   = 'r';
            $newinstance->weight     = 0;
            $newinstance->visible    = 1;
            $newinstance->configdata = '';
            
            //insert offline moodle block to course:
            if(!empty($newinstance->blockid)) {
                // Only add block if it was recognized
                insert_record('block_instance', $newinstance);
            } else {
               notify("insert offline moodle block failed. - no block found");
            }
           fix_course_sortorder(); 
        } else {
            error("error creating course");
        }
        $course->id = $newcourseid;
        $id = $newcourseid;
 }
 

 require_login($course->id, false);
 

 
 //now get $currenthash
 $incrementals = get_records('incremental_instance', 'courseid', $id, 'timecreated');
 
 if (!empty($incrementals) && $currenthash = array_pop($incrementals)) {
     if (isset($currenthash->hash) && isset($currenthash->filename)) {
         while (isset($currenthash->hash) && isset($currenthash->filename) && $currenthash->hash <> $currenthash->filename) { //make sure the record is valid - if both hash and filename are exact - this is related to a client update - not the server backup.
             $currenthash = array_pop($incrementals); //get latest course hash.
         }
     }
 }
 $navlinks = array();
 $navlinks[] = array('name' => get_string('updatecourse','local'), 'link' => '', 'type' => 'activity');
 $navigation = build_navigation($navlinks);

 print_header_simple(get_string('updatecourse','local'), get_string('updatecourse','local'), $navigation, "", "", true,'', '');

 if (empty($currenthash)) {
     print_box(get_string('coursenotlinked', 'local'), 'generalbox', 'intro');
     incremental_manual_download_form($id);
     print_footer($course);
     exit;
 }

 $currenthash = str_ireplace('.zip','',$currenthash->filename); //strip out .zip from filename.
  
 //check XDELTA is installed.
 if (!check_xdelta_installed()) {
     error(get_string('xdeltanotinstalled','local'));
 }

 //get Xdelta path.
 $incremental_config = backup_get_config();
 $xdeltacmd = $incremental_config->backup_inc_pathtoxdelta;

 if (strpos($xdeltacmd, '..') !==false) { 
     //this is a relative path - convert it before using it!
     $xdeltacmd = realpath($CFG->dataroot.$xdeltacmd);
 }
  
 $restorefile = download_incremental($currenthash, $course->id);
     if ($restorefile == 'autofailed') {
         $externalurl = get_field('config_plugins', 'value', 'name', 'backup_inc_server').'?hash='.$currenthash;
         print_box(get_string('autofailed', 'local').'<br><a href="'.$externalurl.'&action=download" target="_blank">click here to download manually</a><br>', 'generalbox', 'intro');
         echo "TODO - must add upload form for incremental here!";
         //incremental_manual_download_form($id);
    } elseif ($restorefile == 'uptodate') {
        print_box(get_string('uptodate','local'), 'generalbox', 'intro');
    } else {
         $prefs['restore_course_files'] = 1;
         $prefs['restore_site_files'] = 1;
         $prefs['restore_user_files'] = 1;
         $pref = restore_generate_preferences_artificially($course, $prefs);
         $pref->updating = 1;

         $status = import_backup_file_silently($CFG->dataroot.'/incrementals_client/'.$id.'/'.$restorefile,$course->id, false, true, $pref);
         if ($status) {
            //insert updated backup hash.
            $bkinstance = new object();
            $bkinstance->courseid = $course->id;
            $bkinstance->filename = $restorefile;
            $bkinstance->hash = $restorefile;
            $bkinstance->timecreated = time();
            if (!insert_record('incremental_instance', $bkinstance)) {
                error('failed inserting incremental_instance record');
            }
            $client_keep = get_field('config_plugins', 'value', 'name', 'backup_inc_client_keep');
            if (!$client_keep) {
                if (!empty($currenthash)) {
                    $currentfile = $currenthash.'.zip';
                    if ($currentfile <> $restorefile) {
                        //delete old hash!
                        unlink($CFG->dataroot.'/incrementals_client/'.$id.'/'.$currentfile);
                    }
                }
            }
            
            print_box(get_string('updatecoursesuccess','local'), 'generalbox', 'intro');
         } else {
            //TODO need to think about what to do now!!  
            error('restore precheck failed.'.$status);
         }
            
    } 
 print_footer($course);

?>