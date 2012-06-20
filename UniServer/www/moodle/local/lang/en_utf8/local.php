<?php //file for incremental_backup_strings.

$string['incrementalbackups'] = 'Incremental Backups';
$string['backupincrementalshelp'] = 'Allow Incremental Backups to be created via manual course backup';
$string['backupscheincrementalshelp'] = 'Run Incremental Backups as a scheduled task via cron';
$string['modules'] = 'Modules';
$string['backupincmoduleshelp'] = 'Which modules to include in automated backups';
$string['incrementalcourses'] = 'Incremental Courses';
$string['addincrementalcoursenote'] = 'Which courses to include in the automated incremental backups';
$string['manualincrementals'] = 'Manual Incrementals';
$string['scheduledincrementals'] = 'Scheduled Incrementals';
$string['pathtoxdelta'] = 'xdelta path'; 
$string['configpathtoxdelta'] = 'Path to Xdelta - usually /usr/bin/xdelta on linux, or somepath under windows';
$string['incserver'] ='Update Server';
$string['configincserver'] ='The location of the update page on the update server for your site, something like: http://localhost/moodle/backup/send_incremental.php';
$string['incrementalclient'] ='Incremental Client';
$string['generateincrementals'] = 'Generate Incrementals';
$string['uptodate'] ='This Course is already up to date!';
$string['updatecourse'] ='Update Course';
$string['updatecoursesuccess'] ='This Course has been updated successfully';
$string['restorethisfile'] = 'Restore this file';
$string['coursenotlinked'] = 'This course is not linked to a central server. - you must download a new course. and then upload it here.<br/> NOTE: this will delete all existing content in your course.'; 
$string['autofailed'] = 'Automatic Download Failed - you must perform a manual update';
$string['xdeltanotinstalled'] = 'XDELTA not installed - incrementals cannot run';
$string['errornobackup'] ='There are no current backups for this course to export. Attempting backup....';
$string['exportcourse'] = 'Export Course';
$string['export'] = 'Export';
$string['privateuserdata'] = 'User Profile Data';
$string['backupalluserdata'] = 'Backup all user data';
$string['hideprivatedata'] = 'Hide private data';
$string['privateuserdatahelp'] = 'This sets whether to include things like passwords and other private information in the backup';
$string['incrementalclientkeep'] ='Keep old backups';
$string['incrementalclientkeepinfo'] ='setting this to true will allow all backups downloaded to be kept locally';
$string['offlinemoodle'] = 'Offline Moodle';
$string['coursenotchanged'] = '<strong>Warning: This course has not changed since the last backup, so this backup has been automatically deleted.</strong>';
$string['existingcourseupdating'] ='Existing course, updating it';
$string['currentcourseupdating'] ='Current course, updating it';
?>