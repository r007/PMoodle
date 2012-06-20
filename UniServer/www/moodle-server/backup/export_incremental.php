<?php
/*
 * This file is called by the offline Moodle client and sends the existing
 * incremental or Full backup if the incremental no longer exists.
 * 
 */    
 
 require('../config.php');
 

 $id = required_param('id',PARAM_INT);   // course
 
 if (! $course = get_record("course", "id", $id)) {
    error("Course ID is incorrect");
 }
 require_login($course->id, false);
 
 require_once("$CFG->dirroot/backup/incremental_backuplib.php");
 require_once("$CFG->dirroot/backup/backuplib.php");
 require_once("$CFG->dirroot/backup/backup_sch_incremental.php");
 require_once("$CFG->dirroot/backup/lib.php");
 require_once("$CFG->dirroot/lib/filelib.php");
  
 //now get $currenthash
 $incrementals = get_records('incremental_instance', 'courseid', $id, 'timecreated');
 
 if (!empty($incrementals) && $currenthash = array_pop($incrementals)) {
     if (isset($currenthash->hash) && isset($currenthash->filename)) {
         while (isset($currenthash->hash) && isset($currenthash->filename) && $currenthash->hash == $currenthash->filename) { //make sure the record is valid - if both hash and filename are exact - this is related to a client update - not the server backup.
             $currenthash = array_pop($incrementals); //get latest course hash.
         }
     }
 }

 if (empty($currenthash)) {
    //need to run backup for this course.
     
     // now do other stuff.!!!
     $navlinks = array();
     $navlinks[] = array('name' => get_string('exportcourse','local'), 'link' => '', 'type' => 'activity');
     $navigation = build_navigation($navlinks);
     print_header_simple(get_string('exportcourse','local'), get_string('exportcourse','local'), $navigation, "", "", true,'', '');
     print_box(get_string('errornobackup','local'), 'generalbox', 'intro');
     $course_status = schedule_backup_launch_inc_backup($course, time());
     if ($course_status) {
        redirect("export_incremental.php?id=".$id);
     } else {
        error("please run a backup manually for this course"); 
     }
     exit;
 }
 $incremental_config = backup_get_config();
 //set directory paths
 if (!empty($incremental_config->backup_inc_destination)) {
     $backuppath = $incremental_config->backup_inc_destination.'/'.$crnt->courseid.'/';
 } else {
     $backuppath = $CFG->dataroot.'/'.$course->id.'/backupdata/';
 }
        
 if (file_exists($backuppath.$currenthash->filename)) {
     send_file($backuppath.$currenthash->filename, $currenthash->hash.'.zip');
 } else {
     error(get_string('errornobackup','local'));
 }
 
  
 ?>
