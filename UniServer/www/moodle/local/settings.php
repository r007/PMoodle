<?php // $Id$

// This file defines settingpages and externalpages under the "courses" category

if ($hassiteconfig
 or has_capability('moodle/site:backup', $systemcontext)
 or has_capability('moodle/category:update', $systemcontext)) { // speedup for non-admins, add all caps used on this page

    // incremental page:
    $bi2 = array();
        $bi2[] = new admin_setting_configcheckbox('backup_incrementals', get_string('manualincrementals', 'local'), get_string('backupincrementalshelp', 'local'), 0); //allow incrementals to be generated in the course backup screen.
        $bi2[] = new admin_setting_configcheckbox('backup_sche_incrementals', get_string('scheduledincrementals', 'local'), get_string('backupscheincrementalshelp', 'local'), 0);
        $bi2[] = new admin_setting_configexecutable('backup_inc_pathtoxdelta', get_string('pathtoxdelta', 'local'), get_string('configpathtoxdelta', 'local'), '/usr/bin/xdelta');

        $bi2[] = new admin_setting_configcheckbox('backup_inc_withuserdata', get_string('includemoduleuserdata'), get_string('backupincludemoduleuserdatahelp'), 0);
        $modules = get_records('modules','visible','1'); //allow certain modules to be selected in the incrementals.
        $tms = array();
        foreach ($modules as $m) {
            $tms[$m->id] = $m->name;
        }
        $bi2[] = new admin_setting_configmultiselect('backup_inc_modules', get_string('modules', 'local'), get_string('backupincmoduleshelp', 'local'), array(), $tms);
        $bi2[] = new admin_setting_configcheckbox('backup_inc_metacourse', get_string('metacourse'), get_string('backupmetacoursehelp'), 0);
        $bi2[] = new admin_setting_configselect('backup_inc_users', get_string('users'), get_string('backupusershelp'),
                                               0, array(0 => get_string('all'), 1 => get_string('course')));
        $bi2[] = new admin_setting_configselect('backup_inc_privatedata', get_string('privateuserdata','local'), get_string('privateuserdatahelp', 'local'),
                                               0, array(0 => get_string('backupalluserdata','local'), 1 => get_string('hideprivatedata','local')));
        $bi2[] = new admin_setting_configcheckbox('backup_inc_logs', get_string('logs'), get_string('backuplogshelp'), 0);
        $bi2[] = new admin_setting_configcheckbox('backup_inc_userfiles', get_string('userfiles'), get_string('backupuserfileshelp'), 0);
        $bi2[] = new admin_setting_configcheckbox('backup_inc_coursefiles', get_string('coursefiles'), get_string('backupcoursefileshelp'), 0);
        $bi2[] = new admin_setting_configcheckbox('backup_inc_sitefiles', get_string('sitefiles'), get_string('backupsitefileshelp'), 0);
        $bi2[] = new admin_setting_configcheckbox('backup_inc_messages', get_string('messages', 'message'), get_string('backupmessageshelp','message'), 0);
        $bi2[] = new admin_setting_configselect('backup_inc_keep', get_string('keep'),
                                               get_string('backupkeephelp'), 10, array(0 => get_string('all'), 1 => '1',
                                                                                                              2 => '2',
                                                                                                              5 => '5',
                                                                                                              10 => '10',
                                                                                                              20 => '20',
                                                                                                              30 => '30',
                                                                                                              40 => '40',
                                                                                                              50 => '50',
                                                                                                              100 => '100',
                                                                                                              200 => '200',
                                                                                                              300 => '300',
                                                                                                              400 => '400',
                                                                                                              500 => '500'));
       
        $bi2[] = new admin_setting_special_backupincdays();
        $bi2[] = new admin_setting_configtime('backup_inc_hour', 'backup_inc_minute', get_string('executeat'),
                                             get_string('backupexecuteathelp'), array('h' => 0, 'm' => 0));
        $bi2[] = new admin_setting_configdirectory('backup_inc_destination', get_string('saveto'), get_string('backupsavetohelp'), '');

        $temp = new admin_settingpage('incrementalbackups', get_string('incrementalbackups','local'), 'moodle/site:backup');
        foreach ($bi2 as $backupitem) {
            $backupitem->plugin = 'incrementalbackup';
            $temp->add($backupitem);
        }
        $ADMIN->add('courses', $temp);
        
        $ADMIN->add('courses', new admin_externalpage('incrementalcourses', get_string('incrementalcourses', 'local'), $CFG->wwwroot . '/local/incremental_courses.php'));

           // incremental client page:
    $bi3 = array();
        $bi3[] = new admin_setting_configtext('backup_inc_server', get_string('incserver', 'local'), get_string('configincserver', 'local'), '');
        $bi3[] = new admin_setting_configcheckbox('backup_inc_client_keep', get_string('incrementalclientkeep', 'local'), get_string('incrementalclientkeepinfo', 'local'), 0);

        $temp = new admin_settingpage('incrementalclient', get_string('incrementalclient','local'), 'moodle/site:backup');
        foreach ($bi3 as $backupitem) {
            $backupitem->plugin = 'incrementalclient';
            $temp->add($backupitem);
        }
        $ADMIN->add('courses', $temp);
         
} // end of speedup
?>