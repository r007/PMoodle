<?php
/*
 * This file is called by the offline Moodle SERVER and sends the existing
 * incremental or Full backup if no incremental is available to the client.
 * 
 */    
 
 require('../config.php');
 require_once("$CFG->dirroot/backup/backup_sch_incremental.php");
 require_once("$CFG->dirroot/backup/incremental_backuplib.php");
 require_once("$CFG->dirroot/backup/backuplib.php");
 require_once("$CFG->dirroot/backup/lib.php");
 require_once("$CFG->dirroot/lib/filelib.php");
 $currenthash = required_param('hash');       // hash
 $action = required_param('action');       // what to do?
 
 $file = get_incremental($currenthash);
 if ($file) {
    if ($action=='curldownload') {   
        readfile_chunked($file->path.$file->name);
    } elseif ($action=='download') {   
        send_file($file->path, $file->name, 864,0,true,true);
    } elseif ($action=='check') {
        echo $file->name;
    } else {
       echo 'no action specified';  
    }
 } else {
     echo 'ERROR!!! No file returned';
 }

?>
