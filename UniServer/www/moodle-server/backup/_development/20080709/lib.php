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
 
 
 
     function createNewCourseData($elements = null){
        $data = new object();
        
        $name = 'Test Course '.date('Ymd H:i');
        $time = time();
        if(!empty($elements) && !empty($elements->name)){
            $name = $elements->name;
        }
        
        if(!empty($elements) && !empty($elements->time)){
            $time = $elements->time;
        }
        
        // Instance specific elements
        $data->category = 1;
        $data->fullname = $name;
        $data->shortname = $name;
        $data->summary = 'test course '.$name;
        
        $data->startdate = $time;
        $data->enrolstartdate = $time;
        $data->enrolenddate = $time;
        
        // Standard course elements
        $data->MAX_FILE_SIZE = 16777216;
        $data->idnumber = null; 
        $data->format = 'weeks';
        $data->numsections = 10;
        $data->hiddensections = 0;
        $data->newsitems = 5;
        $data->showgrades = 1;
        $data->showreports = 0;
        $data->maxbytes = 16777216;
        $data->metacourse = 0;
        $data->enrol = null; 
        $data->defaultrole = 0;
        
        $data->enrollable = 1;
        $data->enrolstartdisabled = 1;
        
        $data->enrolenddisabled = 1;
        $data->enrolperiod = 0;
        $data->expirynotify = 0;
        $data->notifystudents = 0;
        $data->expirythreshold = 864000;
        $data->groupmode = 0;
        $data->groupmodeforce = 0;
        $data->visible = 1;
        $data->enrolpassword = null; 
        $data->guest = 0;
        $data->lang = null; 
        $data->restrictmodules = 0;
        $data->role_1 = null; 
        $data->role_2 = null;
        $data->role_3 = null;
        $data->role_4 = null;
        $data->role_5 = null;
        $data->role_6 = null;
        $data->role_7 = null;
        $data->submitbutton = 'Save changes';
        $data->id = 0;
        $data->teacher = 'Teacher';
        $data->teachers = 'Teachers';
        $data->student = 'Student';
        $data->students = 'Students';
        
        return $data;
    }
    
    function setFormSubmitted($form){
        
        if(empty($form)){
            return false;
        }
        
        
    }
    
    
   function createCourse($categoryId){
        
        global $Out;
        
        $course = null;
        $category = null; // Must define as the compact function below expects a category variable 
     
        if ($categoryId) { // creating new course in this category
            require_login();
            if (!$category = get_record('course_categories', 'id', $categoryId)) {
                error('Category ID was incorrect');
            }
            require_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $category->id));
        } else {
            require_login();
            error('Either course id or category must be specified');
        }
        
        // We're pretending a form has been submitted
        // Create the form
        $editform = new course_edit_form('edit.php', compact('course', 'category'));
        $editform->_form->_flagSubmitted = true;
        
        // Set the values for certain elements
        $elements = new object();
        $elements->name = 'SRV '.date('Ymd H:i');
        $elements->time = time();
        
        // Create a data object of elements for the form
        $data = createNewCourseData($elements);
        
        // Set the form with the data object
        $editform->set_data($data);
        
        //$elements = $editform->_form->_elements;
        //$Out->print_r($elements, '$elements (1) = ', 0, true);
        
        // Set the submit data as the form checks these values match the form elements
        $submitData = $editform->_form->_submitValues = (array)$data;
        //$Out->print_r($submitData, '$submitData (1) = ', 0, true);
        
        $data = $editform->get_data();
    
        // Password fields must match
        $data->password = $data->enrolpassword;  // we need some other name for password field MDL-9929
    
        //preprocess data
        if ($data->enrolstartdisabled){
            $data->enrolstartdate = 0;
        }
    
        if ($data->enrolenddisabled) {
            $data->enrolenddate = 0;
        }
    
        $data->timemodified = time();
    
        // Now create the course from the form
        if (!$course = create_course($data)) {
            print_error('coursenotcreated');
        }

        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        // assign default role to creator if not already having permission to manage course assignments
        if (!has_capability('moodle/course:view', $context) or !has_capability('moodle/role:assign', $context)) {
            role_assign($CFG->creatornewroleid, $USER->id, 0, $context->id);
        }

        // ensure we can use the course right after creating it
        // this means trigger a reload of accessinfo...
        mark_context_dirty($context->path);
        print_box('course successfully created.');
    
        return $course;
    }
    
       function backupCourseByCourseId($courseId){
        
        global $SynchManager;
        global $Out;
        
        // Ok the course has now been created so let's backup it up
        $sessionId = null;
         
         $type = synch_view_controller::$TYPE_ID_COURSE;
         
         /*
         * Begin the process of restoring the session
         * 
         * Load the session using the session id. If no session exists, create one.
         */
        $session = synch_Session_controller::loadSession($sessionId);
         
         //$Out->append('$type = '.$type);
         //$Out->flush();
         $created = $SynchManager->createBackupByIdAndType($session, $courseId, $type);
    }
?>
