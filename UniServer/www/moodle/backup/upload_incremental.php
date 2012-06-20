<?php

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
 
 $id = required_param('id',PARAM_INT);   // course
 
 if (! $course = get_record("course", "id", $id)) {
    error("Course ID is incorrect");
 }

 require_login($course->id, false);
 
 global $CFG;

 $dir = $CFG->dataroot.'/incrementals_client/'.$id;
    
 if (!check_dir_exists($dir, true, true)) { //now create folder for new incremental
     error('failed to create dir for new incremental'.$dir);
 }

 require_once($CFG->dirroot.'/lib/uploadlib.php');
 $um = new upload_manager('newfile',false,true,$id,false,0,true);
 if ($file = $um->process_file_uploads($dir)) {          
         $pref = new StdClass;
         $pref->updating = 1;
         $status = import_backup_file_silently($um->files['newfile']['fullpath'],$course->id, true, true, $pref);
         if ($status) {
            $navlinks = array();
            $navlinks[] = array('name' => get_string('updatecourse','local'), 'link' => '', 'type' => 'activity');
            $navigation = build_navigation($navlinks);
           
            print_header_simple(get_string('updatecourse','local'), get_string('updatecourse','local'), $navigation, "", "", true,'', '');
            //insert updated backup hash.
            $bkinstance = new object();
            $bkinstance->courseid = $course->id;
            $bkinstance->filename = $um->files['newfile']['name'];
            $bkinstance->hash = $um->files['newfile']['name'];
            $bkinstance->timecreated = time();
            if (!insert_record('incremental_instance', $bkinstance)) {
                error('failed inserting incremental_instance record');
            }
           print_box(get_string('updatecoursesuccess','local'), 'generalbox', 'intro');
         //  print_footer($course);
         } else {
            //TODO need to think about what to do now!!  
            error('restore failed.'.$status);
         }    
 } else {
     error("no file uploaded!");
 }
?>