<?php
/*
 * This file is called by the offline Moodle client and sends the existing
 * incremental or Full backup if the incremental no longer exists.
 * 
 */    
 
 require('../../../config.php');
 global $Out;
 
 $pathId = optional_param('pathId', 1, PARAM_INT);  
 
 // Setup the standard path
 $backupPath = '/backup/send_incremental.php';
 $host = '';
 
 // Set the server paths 
 switch($pathId){
     case 1: 
        $host = 'http://localhost/moodle-server';
        break;
     case 2:
        $host = 'http://cc5983.vledev.open.ac.uk/offline/development/moodle/20080528';
        break;
     case 3:
        $host = 'http://cc5983.vledev.open.ac.uk/offline/development/moodle/ou-moodle-merge';
 }
 
 // Append the standard path stuff
 $newserverlocation = $host.$backupPath;
 
 // Update the db. 
 $config_plugins = get_record('config_plugins','name', 'backup_inc_server');
 $config_plugins->value = $newserverlocation;
 update_record('config_plugins', $config_plugins);
 
 print_box('The backup_inc_server path is now '.$newserverlocation, 'generalbox', 'intro');
 ?>