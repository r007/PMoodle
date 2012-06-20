<?php
/*
 * This file is called by the offline Moodle client and sends the existing
 * incremental or Full backup if the incremental no longer exists.
 * 
 */    
 
 require('../../../config.php');
 global $Out;
 
 // Setup the standard path
 $backupPath = '/backup/send_incremental.php';
 
 // Set the server paths 
 $localPath = 'http://localhost/moodle-server';
 $vledevPath = 'http://cc5983.vledev.open.ac.uk/offline/development/moodle/20080528';
 $ofoumePath = 'http://cc5983.vledev.open.ac.uk/offline/development/moodle/ou-moodle-merge';
 
 // Set the actual path to use
 $newserverlocation = $ofoumePath;
 
 // Append the standard path stuff
 $newserverlocation .=$backupPath;
 
 // Update the db. 
 $config_plugins = get_record('config_plugins','name', 'backup_inc_server');
 $config_plugins->value = $newserverlocation;
 update_record('config_plugins', $config_plugins);
 
 print_box('The backup_inc_server path is now '.$newserverlocation, 'generalbox', 'intro');
 ?>