<?PHP // $Id: view.php,v 1.157 2007-07-14 03:00:36 mjollnir_ Exp $

//  Display profile for a particular user
    require_once dirname(__FILE__)."/setup.php";
    //require_once("../config.php");
    require_once($CFG->dirroot.'/user/profile/lib.php');
    $id      = optional_param('id',     0,      PARAM_INT);   // user id
    $course  = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)
    $clearCache  = optional_param('clearCache', 0, PARAM_BOOL);   // clear cached info

    if (empty($id)) {         // See your own profile by default
        require_login();
        $id = $USER->id;
    }

    if (! $user = get_record("user", "id", $id) ) {
        error("No such user in this course");
    }

    if (! $course = get_record("course", "id", $course) ) {
        error("No such course id");
    }

	// Course is site for now. Get the context
    $coursecontext = get_context_instance(CONTEXT_SYSTEM);   // SYSTEM context
    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
   
    if (!has_capability('moodle/user:update', $sitecontext) and !has_capability('moodle/user:delete', $sitecontext)) {
        error('You do not have the required permission to edit/delete users.');
    }
    
    if($clearCache){
    	// Go through all the locations of cached info and clear them out. Includes sessions, queues
        
        GLOBAL $CFG;
        FileSystem::deleteFolder($CFG->synch->path_queue_in);
        FileSystem::deleteFolder($CFG->synch->path_queue_out);
        FileSystem::deleteFolder($CFG->synch->path_sessions);
    }

    if (empty($CFG->loginhttps)) {
        $securewwwroot = $CFG->wwwroot;
    } else {
        $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
    }
    
    $featuretitle = get_string('synch', 'synch');
    $pagetitle = get_string('synchronise', 'synch'); 

/// We've established they can see the user's name at least, so what about the rest?
	GLOBAL $Out;

    /// Print header.
    $navlinks = array();
    $navlinks[] = array('name' => $featuretitle, 'link' => 'index.php', 'type' => 'activity');
    
    $navigation = build_navigation($navlinks);
    
    print_header_simple(format_string(' '.$featuretitle.': '.$pagetitle), "",
                 $navigation, '', '', true);
                 
/// OK, security out the way, now we are showing the user

    add_to_log($course->id, "synch", "synch", "synch.php?id=$user->id&course=$course->id", "$user->id");

    if ($course->id != SITEID) {
        if ($lastaccess = get_record('user_lastaccess', 'userid', $user->id, 'courseid', $course->id)) {
            $user->lastaccess = $lastaccess->timeaccess;
        }
    }
    

    

    
	include(dirname(__FILE__).'/view/index.php');
    
	require_once dirname(__FILE__)."/teardown.php";
	
    print_footer($course);
    
    

/// Functions ///////

function print_row($left, $right) {
    echo "\n<tr><td class=\"label c0\">$left</td><td class=\"info c1\">$right</td></tr>\n";
}

?>
