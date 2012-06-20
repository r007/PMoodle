<?PHP 
 /**
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * This  file is used to both provide a simple interface to manage
 * synchronisation and also trigger the processes of synchronisation
 */

    /*
     * Load the necessary Moodle and Synch files to setup a synching environment
     */
    require_once dirname(__FILE__)."/setup.php";
    
    require_once($CFG->dirroot.'/synch/modal/Modal.php');
    require_once($CFG->dirroot.'/synch/modal/ContentItem.php');
    require_once($CFG->dirroot.'/synch/modal/ContentHierarchy.php');
    
    $id      = optional_param('id',     0,      PARAM_TEXT);  // user id
    $clearCache    = optional_param('clearCache',     0,      PARAM_BOOL);  // user id
    $remoteServerId      = optional_param('remoteServerId',     0,      PARAM_TEXT);  // server id
    
    // If we're on a secure page ensure all urls are secure to prevent browser error
    $securewwwroot = synch_get_http_prefix();
    
    /*
     * Setup the navigation bar and header
     */
    $featuretitle = get_string('synchronise', 'synch');
    $pagetitle = get_string('find_content', 'synch'); 

	$nav = '<a href="'.$securewwwroot.'/synch/">'.get_string('synchronise', 'synch').'</a> -> '.$pagetitle;
    
    /// Print header.
    $navlinks = array();
    $navlinks[] = array('name' => $featuretitle, 'link' => 'index.php', 'type' => 'activity');
    $navlinks[] = array('name' => format_string($pagetitle), 'link' => '#', 'type' => 'activityinstance');
    
    $navigation = build_navigation($navlinks);
    
    print_header_simple(format_string(' '.$featuretitle.': '.$pagetitle), '',
                 $navigation, '', '', true, '&nbsp;', null, null, 'onload="initialiseSynchTree()"');
    
    add_to_log($id, 'synch', 'synch', 'find_content.php?id='.$id, null);
    
    /*
     * Prepare the objects to find content
     */
     
    // Create a synch hierarchy object
    $SynchContentHierarchy = new SynchContentHierarchy();
    GLOBAL $SynchContentHierarchy, $SynchManager, $SynchServerController;

    /*
     * The synchmanager for the moment has it's own server objects that are used
     * to control the process of finding content. These need to be setup.
     * 
     * First we need to set the remote server id
     */    
    $SynchServerController->setRemoteServerId($remoteServerId);
    $SynchServerController->createAndAppendDefaultServers();
    
    if(!$clearCache){
        // Load cached data
        $SynchViewController->restoreHierarchy();
    }
    
    //Check the hierarchy has the minimum required information and load any new data 
    $SynchViewController->prepareHierarchy($id, $remoteServerId);
    
    // Cache the hierarchy that has been built
    $SynchViewController->backupHierarchy();

    /*
     * display the page
     */
	include(dirname(__FILE__).'/view/find_content.php');
	
	require_once dirname(__FILE__)."/teardown.php";
	
    print_footer();
    
?>
