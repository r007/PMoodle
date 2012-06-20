<?php
 /*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * File description to go here
 */
 
     require_once('../../../config.php');
    require_once($CFG->dirroot.'/enrol/enrol.class.php');
    require_once($CFG->libdir.'/blocklib.php');
    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->dirroot.'/course/edit_form.php');
    require_once($CFG->dirroot.'/synch/setup.php');
    require_once($CFG->dirroot.'/synch/setup.php');
    require_once dirname(__FILE__).'/lib.php';
    

    
    global $Out;
    
    define ('ACTION_CREATE_COURSE', 1);
    define ('ACTION_BACKUP_COURSE', 2);
    
    $action = optional_param('action', ACTION_CREATE_COURSE, PARAM_INT);       // what to do. default = Create a course  
    $categoryId = optional_param('categoryid', 1, PARAM_INT);       // what category to create the course in
    $courseId = optional_param('courseId', 0, PARAM_INT);       // what category to create the course in
     
    $Out->print_r($action, '$action (1) = ');
    
    if($action & ACTION_CREATE_COURSE){
        $Out->append('Creating course');
        $course = createCourse($categoryId);
      
        if(isset($course->id)){
            $courseId = $course->id;
        }
    }
    
    if($action & ACTION_BACKUP_COURSE){
    	$Out->append('Backing up course');
        backupCourseByCourseId($courseId);
    }
 
    $Out->flush(); // Output stream to screen. 
?>
