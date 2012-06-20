<?php
/*
 * Created on 3/06/2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
require_once(dirname(__FILE__) . '/../../config.php');

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php'); // Include the test libraries
require_once($CFG->dirroot . '/backup/lib.php'); // Include the code to test
require_once($CFG->dirroot . '/backup/incremental_backuplib.php'); // Include the code to test
require_once($CFG->dirroot . '/backup/backuplib.php'); // Include the code to test
require_once($CFG->dirroot . '/backup/restorelib.php'); // Include the code to test
require_once($CFG->dirroot . '/backup/backup_sch_incremental.php'); // Include the code to test

/** This class contains the test cases for the functions in editlib.php. */
class incremental_editlib_test extends UnitTestCase {
    function setUp() {
       //create backup of site course to generate incrementals against.
       $errorstr = '';
       $backupfile = backup_course_silently(1, array(), $errorstr);
    
    }
    function test_get_list_backups() {
        $backuplist =get_list_backups('1'); 
        if (!empty($backuplist)) {
            $this->assertTrue(true);
        }
        
    }
    function test_incremental_backup() {
        // Do the test here/
        $course = get_record('course', 'id', '1');
        $course_status = schedule_backup_launch_inc_backup($course,time());
        $this->assertTrue($course_status);
        
    }
    function test_get_incremental() {
        
        $crnt_course = get_records('incremental_instance', 'courseid', '1', 'timecreated');
        //print_object($crnt_course);
        $newbkup = array_pop($crnt_course); //get latest course hash.
        while ($newbkup->hash == $newbkup->filename) { //make sure the record is valid - if both hash and filename are exact - this is related to a client update - not the server backup.
            $newbkup = array_pop($crnt_course); //get latest course hash.
        }
        $returnfile = new object();

        $incremental = get_incremental($newbkup->hash);
        
        if ($incremental->name == 'uptodate') { 
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }    
}

?>
