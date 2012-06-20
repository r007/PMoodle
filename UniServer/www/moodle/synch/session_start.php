<?PHP 

/*
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
    
    $id      = optional_param('id',     0,      PARAM_TEXT);  // data item id
    $synch   = optional_param('synch',     0,      PARAM_BOOL);  // should the synchronisation process begin
    $sessionId   = optional_param('sessionId',     0,      PARAM_TEXT);  // session id

    /*
     * @var stdClass $page a simple object to store page related values.
     */
    $page = new stdClass;
    /*
     * @var string $page->sessionId sessions are managed by id. From the id we
     * can restore the session.
     */
    $page->sessionId = $sessionId;
    /*
     * @var string $page->id The data item id that we are about to synchronise.
     * Data item id's contain data about the type of content they represent and
     * correspondingly denote the moodle db table they refer to along with the
     * Moodle instance ie offline moodle or the remote moodle.
     */
    $page->id = $id;

    // Log that we've made it this far
    add_to_log($id, 'synch', 'synch', 'session_start.php?id='.$id, null);
    
    /*
     * Begin the process of restoring the session
     * 
     * Load the session using the session id. If no session exists, create one.
     */
    $session = synch_Session_controller::loadSession($page->sessionId);
 
     /*
     * Assign the session object to the page object to make it easy to find
     * within the page
     */
    $page->session = $session;
       
    /*
     * Load the session object with details that define the synchronisation
     * session. the intention is that once the details have been defined once in
     * the session they shouldn't need to be defined again and will be
     * available through out the session. Currently they are defined every time.
     * This will be addressed in future.
     * 
     * @var string $session->dataItemId data item id for the current session
     */
    $session->dataItemId = $page->id;
    
    /*
     * We need to know which servers we are dealing with. In this instance
     * Offline Moodle is the local server. firstly we need to get their ids but
     * we don't want to have to ask the remote server directly if we don't have
     * to.
     * 
     * @var int $session->localServerId id of the local moodle instance obtained
     */
    $session->localServerId = $SynchServerController->getServerId();
    /*
     * @var int $session->remoteServerId get the server it from the data item id
     * to save a call to the remote server
     */
    $session->remoteServerId = $SynchServerController->getServerIdFromId($page->id);
    
    /*
     * @var stdClass $session->servers Create a session object to record
     * necessary server details and create both a local and a remote server
     * object
     */
    $session->servers = new stdClass;
    $session->servers->{$session->localServerId} = new stdClass; 
    $session->servers->{$session->remoteServerId} = new stdClass; 
    
    /*
     * Tell the synchronisation manager what the remote server id is. This
     * should probably be deprecated in time in favour of the server objects
     * being passed around and interrogated instead
     */
    $SynchServerController->setRemoteServerId($session->remoteServerId);
    
    /*
     * Simple flag stored in synchmanager for now recording whether there are
     * changes to merge from the local or remote server. Will  be used when
     * merging is developed further. Whether this should be stored in the synch
     * manager is debatable. Works for now. We also just set it to true to now.
     * We just wanted a mechanism that would work in the future that provided a
     * quick check.
     */
    $SynchManager->setSessionHasChangesByServerId(true, $session->localServerId, $session);
    $SynchManager->setSessionHasChangesByServerId(true, $session->remoteServerId, $session);
    
    /*
     * The synchmanager for the moment has it's own server objects that are used
     * to control the synch process. These need to be setup.
     */
    $SynchServerController->createAndAppendDefaultServers();
    
    /*
     * At this point we are simply synching at the course level. We need to
     * get the details of the item we want to synch so we can display them. 
     * 
     * @var mixed $item an object to hold the details of the item being
     * synchronised
     */
    $item = null;
    
    /*
     * To minimise db and web service use the item details are stored to the
     * session. So check there first.
     */
    if($session->dataItems && isset($session->dataItems[$session->dataItemId])){
        $item = $session->dataItems[$session->dataItemId];
    }
    
    /*
     * If the details aren't in the session, maybe they are available locally
     * because the item may have been downloaded before. We are only synching at
     * the course level for now so just check the db course table.
     */
    if(empty($item)){
        $item = get_record('course', 'id', $page->id);
    }
    
    /*
     * If we still can't find the details we will have to do ask the remote
     * server for them. It's expensive but we have no choice.
     */
    if(empty($item)){
        $item = $SynchManager->getRemoteDetailsByDataItemId($session->dataItemId);
    }
    
    /*
     * Create an object to hold the data specific to the synchronisation process
     */
    $page->synchItem = new SynchSessionItem();
    
    /*
     * If the item details have been found, populate the synch item with the
     * necessary details
     */
    if(!empty($item)){
        $page->synchItem->dataItems[$session->dataItemId] = $item;
        $page->synchItem->id = $item->id;
        $page->synchItem->title = $item->fullname;
        $page->synchItem->type = 'course';
        $page->synchItem->summary = $item->summary;
        $page->synchItem->lastSynchronised = 'n/a';
    }

    /*
     * Has the user asked to start the synchronisation process?
     */
    if($synch){
        
        /*
         * The basic process of synchronisation (ignoring all sorts of
         * background info) is thus:
         * 
         * @ 1)find the unique id of the course to be synchronised
         * @ 2) create a local and a remote backup and store them in the local
         * synch folder by session id
         * @ 3) create  a merged backup file from the local and remote backups
         * @ 4) restore the merged backup to the local and remote servers
         * 
         * There are hundreds of steps in between so the above is simply a guide
         * to make the process understandable. Please be aware that certain
         * areas, specifically the merging and the filtering methods to remove
         * sensitive data have not been developed at this time due to time
         * constraints.  
         * 
         * The focus so far is to have a working version that proves that it is
         * possible to perform the complex tasks such as communicating the
         * process between two moodles, getting the data in and out. The order
         * of priorities has been thus:
         * 
         * @ 1) Get data in and out of moodle in a standard fashion that allows
         * for merging 
         * @ 2) Get separate Moodles communicating via a web services layer 
         * @ 3) Provide a simple process to pass large files securely between
         * Moodles 
         * @ 4) Provide a simple installer package to make distribution, use and
         * maintenance of Offline Moodle easy
         * @ 5) Provide a simple usable and accessible interface to manage the
         * process via Moodle
         * @ 6) Merge the data from local and remote sources 
         * @ 7) Filter out sensitive data from the data that gets passed
         * 
         * So you can see that these things are on the todo list but now that we
         * have something that proves the process is possible. these extra
         * features can be developed in due course. 
         * 
         * It is fair to say that we don't have a synchronisation process until
         * we can effectively merge. That's true. The process for doing this
         * in the forums was the focus of our initial proof of concept code
         * which is available at the Project Development Course at http: //hawk.
         * aos.ecu. edu/moodle/course/view. php?id=22 in the downloads section
         * as 'Proof of Concept'.
         * 
         * This is a development release. Therefore debugging is not a thing of
         * the past. This synchroinsation page and the methods it uses have only
         * recently been developed. Flags were created to make it easy to run
         * only specific methods rather than the entire process to assist with
         * debugging and development. These flags have been left in to assist
         * any keen developers in following the same process in order to
         * understand more about what is happening.
         * 
         * The most important thing that hasn't been finished at this point is
         * merging. The intention is to go through the XML files from the remote
         * and local backups and use the process developed in the proof of
         * concept to make the decisions necessary to create a merge. In
         * situations where automatic merging of details is not possible manual
         * processes have been considered and will be provided. The emphasis
         * though is always on making things as automatic as possible and this,
         * in the case of forums, certainly seems possible. 
         */
         
        // Bitwise flags used to determine whether a method should be called 
        define('SYNCH_CREATE_LOCAL_BACKUP', 1);
        define('SYNCH_RETRIEVE_REMOTE_BACKUP', 2);
        define('SYNCH_MERGE_BACKUPS', 4);
        define('SYNCH_RESTORE_MERGED_BACKUP', 8);
        define('SYNCH_RESTORE_MERGED_BACKUP_TO_REMOTE_HOST', 16);
        
        /*
         * Since the merging process is not in place all that currenlty happens
         * is that a merge file is created directly from a copy of either the
         * local or remote backup file. This atleast allows the overall process
         * to run seamlessly. True merging will be added in time. 
         * 
         * To make this process easier to manage A few bitwise variables have
         * been created to make it easier to tell the synchronisation process to
         * either get a local backup and restore to the remote server or vice
         * versa. 
         */
         
        /*
         * @var int $synchStandard what is the minimum process that must be run.
         */
        $synchStandard = SYNCH_MERGE_BACKUPS;
        /*
         * @var int $hubToOffline what extra must we run to generate a remote
         * backup and restore this locally.
         */
        $hubToOffline = SYNCH_RETRIEVE_REMOTE_BACKUP + SYNCH_RESTORE_MERGED_BACKUP;
        /*
         * @var int $offlineToHub what extra must we run to generate a local
         * backup and restore this on the remote server.
         */
        $offlineToHub = SYNCH_CREATE_LOCAL_BACKUP + SYNCH_RESTORE_MERGED_BACKUP_TO_REMOTE_HOST;
        
        /*
         * By default we generate a remote backup and restore this locally 
         */
        //$bitwise = $synchStandard + $offlineToHub;
        $bitwise = $synchStandard + $hubToOffline;
        
        /*
         * Work needs ot be done to speed up the process of generating and
         * transferring backups. Until then allow a decent amount of time. Set
         * max execution time to 5 minutes to be safe.
         * 
         * @var int $originalMaxExecutionTime store the original max execution
         * time so it can be restored when necessary
         * 
         */
        $originalMaxExecutionTime = ini_get('max_execution_time');
        /*
         * @var int $currentMaxExecutionTime defined as a variable so it can be
         * queried if necessary. Should be moved to configs in due course.
         */
        $currentMaxExecutionTime = 300;
        set_time_limit($currentMaxExecutionTime);
        
        // create a method in the synch manager to generate a backup according to an id. 
        $session->files = array('local'=>null, 'remote'=>null);
        $files = $session->files;
        /*
         * If the item exists locally create a backup in the session backup
         * folder
         */
        if($bitwise & SYNCH_CREATE_LOCAL_BACKUP){
            $localBackup = $SynchManager->createLocalBackup($page->id, $page->session);
            $files['local'] = $localBackup;
        }  
        
        /*
         * If the item exists on the remote server. Create a backup and
         * download it to the session backup folder
         */
        if($bitwise & SYNCH_RETRIEVE_REMOTE_BACKUP){    
            $remoteBackup = $SynchManager->retrieveRemoteBackup($page->id, $page->session);
            $files['remote'] = $remoteBackup;
        }   
        
        /*
         * Merge  the backups. If only one exists the merge is not necessary
         * and the existing backup file becomes the merge file.
         */
        if($bitwise & SYNCH_MERGE_BACKUPS){
            $mergedBackup = $SynchManager->mergeBackups($page->id, $files, $page->session);
            $files['merged'] = $mergedBackup;
        }
        
        /*
         * Restore the merged backup to Offline Moodle
         */
        if($bitwise & SYNCH_RESTORE_MERGED_BACKUP){
            $restoredMergedBackup = $SynchManager->restoreMergedBackup($page->id, $files, $page->session);
        }
        
        /*
         * Restore the merged backup to the remote server
         */
        if($bitwise & SYNCH_RESTORE_MERGED_BACKUP_TO_REMOTE_HOST){
        	$restoredMergedBackupToRemoteHost = $SynchManager->restoreMergedBackupToRemoteHost($page->id, $files, $page->session);
        }
        
        /*
         * Clean up afterwards. Remove the backup files that have been created.
         */
        $SynchManager->clearSessionBackups($page->session);
        
        /*
         * Restore max execution time to its original value
         */
        set_time_limit($originalMaxExecutionTime);
        
        // We don't have merging working so assume the synching worked and set the completed flag in the session
        $page->session->finished = true;
    }
    
    // Save the session
    synch_Session_controller::saveSession($session);
	
    // If we're on a secure page ensure all urls are secure to prevent browser error
    $securewwwroot = synch_get_http_prefix();
    
    $featuretitle = get_string('synchronise', 'synch');
    if($page->session->finished){
        $pagetitle = get_string('session_finish', 'synch');
    }
    else{
    	$pagetitle = get_string('session_start', 'synch');
    }

    // Create the navigation menu
    $nav = '<a href="'.$securewwwroot.'/synch/">'.get_string('synchronise', 'synch').'</a> -> '.$pagetitle;
    
    /// Print header.
    $navlinks = array();
    $navlinks[] = array('name' => $featuretitle, 'link' => 'index.php', 'type' => 'activity');
    $navlinks[] = array('name' => format_string($pagetitle), 'link' => 'session_start.php?id='.$page->id, 'type' => 'activityinstance');
    
    $navigation = build_navigation($navlinks);
    
    print_header_simple(format_string(' '.$featuretitle.': '.$pagetitle), '',
                 $navigation, '', '', true);
                 
    // The same page manages the entire session. Are we at the start or the end. Show the relevant page.
    if($page->session->finished){
    	include(dirname(__FILE__).'/view/session_finish.php');
    }
    else {
	   include(dirname(__FILE__).'/view/session_start.php');
    }
	
	require_once dirname(__FILE__)."/teardown.php";
	
    print_footer();
    
?>
