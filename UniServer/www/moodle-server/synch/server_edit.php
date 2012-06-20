<?PHP 
/*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * This  page handles configuration of trusted servers used by Offline Moodle
 */

//  Display profile for a particular user
    require_once dirname(__FILE__)."/admin/synch-setup.php";
    require_once($CFG->libdir.'/adminlib.php');
    //require_once("../config.php");
    require_once($CFG->dirroot.'/user/profile/lib.php');
    
    $page = new stdClass;
    $id      = optional_param('id',     0,      PARAM_INT);   // user id
    $hubId      = optional_param('hubId',     0,      PARAM_INT);   // hub id
    $course  = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)
    $page->action = optional_param('action', 3, PARAM_INT);   // action: Create 1, Read 2, Update 3, Delete 4
    $page->server = new synch_modal_Server();
    $page->updated = false;
    $defaultReturnUrl = '/synch/find_content.php';
    
    $hubId = SynchContentHierarchy::getIdFromDataItemId($hubId);// Convert the dataItemId to an id
    $page->server->serverId=$hubId;
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
        $object = $page->server->toObject();
        
        if (isset($form->id)) {
            $form->id = clean_param($form->id, PARAM_NOTAGS);
            $object->id = $form->id;
        }
        
        if (isset($form->mnetHostId)) {
            $form->mnetHostId = clean_param($form->mnetHostId, PARAM_NOTAGS);
            $object->mnetHostId = $form->mnetHostId;
        }
        
        
        
        if (isset($form->description)) {
            $form->description = clean_param($form->description, PARAM_TEXT);
            $object->description = $form->description;
        }
        
        
        // action is create so remove the id and add
        if($page->action == SYNCH_ACTION_CREATE){
            $passedValidation = true;
             $page->server->fromObject($object);
            // Get the serverId from the remote host
            if(empty($object->serverId)) {
                $details = $SynchManager->getServerDetailsFromRemoteHost($page->server);
                if(!empty($details['id'])){
                    $object->serverId = $details['id'];
                }
            }
            if(empty($object->serverId)){
                $passedValidation = false;
            }
            if($passedValidation){
                $object->id = insert_record('synch_servers', $object);
                if(!empty($object->id)){
                    $page->message = 'The server has been successfully created. Continue <a href="'.$securewwwroot.$defaultReturnUrl.'" >selecting content</a>';
                    $page->action == SYNCH_ACTION_READ;
                    //$page->server->id = $object->id;
                }
            }
        }
        else if($page->action == SYNCH_ACTION_UPDATE){ // action is update
        	if(update_record('synch_servers', $object)){
        		$page->message = 'The server has been successfully updated. Continue <a href="'.$securewwwroot.$defaultReturnUrl.'" >selecting content</a>';
                $page->action == SYNCH_ACTION_READ;
        	}
        }
        
        // Update the server object with the new parameters
        $page->server->fromObject($object);
    }


    $empty = synch_empty($page->server->id);
    if(synch_empty($page->server->serverId)){ // Adding a new host
        $page->action = 1; //create
    }
    else {
        // If there is an id Get the server details from the db
        $record = get_record('synch_servers', 'serverId', $page->server->serverId);
        
        // Update the server object with the new parameters
        $page->server->fromObject($record);
        $page->action = 3; //update
    }
    
/// We've established they can see the user's name at least, so what about the rest?
    $featuretitle = get_string('synch', 'synch');
    $pagetitle = 'Edit Server'; 
    $nav = '<a href="'.$securewwwroot.'/synch/">'.get_string('synchronise', 'synch').'</a> -> '.$pagetitle;
/// If the user being shown is not ourselves, then make sure we are allowed to see them!

    /// Print header.
    $navlinks = array();
    $navlinks[] = array('name' => $featuretitle, 'link' => 'index.php', 'type' => 'activity');
    $navlinks[] = array('name' => format_string($pagetitle), 'link' => 'server_edit.php?hubId='.$page->server->serverId, 'type' => 'activityinstance');
    
    $navigation = build_navigation($navlinks);
    
    print_header_simple(format_string(' '.$featuretitle.': '.$pagetitle), '',
                 $navigation, '', '', true);

    // Get a list of mnet hosts that excludes those that have a server connection already defined.
    global $CFG;
    
    // Get the mnet ids of defined servers for synching
    $sql = 'SELECT mnetHostId FROM '.$CFG->prefix.'synch_servers';
    $records = get_records_sql($sql);
    $excludeServerIds = array();
    if(is_array($records)){
        foreach($records as $record){
        	if($record->mnetHostId !=$page->server->mnetHostId){
                $excludeServerIds[] = $record->mnetHostId;;
            }
        }
    }
    if(!is_array($excludeServerIds) || !count($excludeServerIds)){
    	$excludeServerIds = null;
    }
    
    // Get the mnet hosts to link to a particular server id
    $hosts = $SynchManager->getMnetHosts($excludeServerIds);

    if (empty($hosts)) {
        $hosts = array();
    }
    
    include('view/server_edit.php');
	require_once dirname(__FILE__)."/admin/synch-teardown.php";
	
    print_footer($course);
    
?>
