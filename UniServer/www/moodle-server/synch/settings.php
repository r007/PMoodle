<?PHP 
/*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * This  page provides an interface to control the settings of the Offline
 * Moodle component
 */

//  Display profile for a particular user
    require_once dirname(__FILE__)."/setup.php";
    require_once($CFG->libdir.'/adminlib.php');
    //require_once("../config.php");
    require_once($CFG->dirroot.'/user/profile/lib.php');
    
    $page = new stdClass;
    $id      = optional_param('id',     0,      PARAM_INT);   // user id
    $course  = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)
    $page->action = optional_param('action', 3, PARAM_INT);   // action: Create 1, Read 2, Update 3, Delete 4
    //$page->server = new synch_modal_Server();
    $page->updated = false;
    $defaultReturnUrl = '/synch/find_content.php';
    
    //$page->server->id=$hubId;
    admin_externalpage_setup('mnetpeers');
    
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

    if (empty($CFG->loginhttps)) {
        $securewwwroot = $CFG->wwwroot;
    } else {
        $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
    }
    
    /// If data submitted, process and store
    if (($form = data_submitted()) && confirm_sesskey()) {
        if(isset($form->serverId)) {
            $form->serverId = clean_param($form->serverId, PARAM_NOTAGS);
            $page->serverId = $form->serverId;
        }
        
        if($page->action == SYNCH_ACTION_UPDATE){ // action is update
        	
            if(set_config('synch_server_id', $page->serverId)){
                $page->message = 'The settings have been successfully updated.';
            }
        }
        
    }

    global $Out;
    //$Out->append('$page->serverId = '.$page->serverId);
    if(synch_empty(@$page->serverId)){
    	$page->serverId = get_config(null, 'synch_server_id');
    }
    
/// We've established they can see the user's name at least, so what about the rest?
    $featuretitle = get_string('synch', 'synch');
    $pagetitle = 'Settings'; 

/// Print header.
    $navlinks = array();
    $navlinks[] = array('name' => $featuretitle, 'link' => 'index.php', 'type' => 'activity');
    $navlinks[] = array('name' => format_string($pagetitle), 'link' => 'settings.php?id='.$page->serverId, 'type' => 'activityinstance');
    
    $navigation = build_navigation($navlinks);
    
    print_header_simple(format_string(' '.$featuretitle.': '.$pagetitle), '',
                 $navigation, '', '', true);

    include('view/settings.php');
	
	require_once dirname(__FILE__)."/teardown.php";
	
    print_footer($course);
    
?>
