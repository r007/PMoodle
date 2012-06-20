<?php
/*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * This  file holds the general configuration settings for the synchronisation
 * component. It also sets up and intialises key objects.
 */
 
	require_once(dirname(__FILE__).'/../config.php');
	GLOBAL $CFG;

    /*
     * We are being lazy for now and including everything. In time this should
     * become more specific to improve performance
     */
	require_once $CFG->dirroot.'/synch/import_all.php';
	
	//$CFG->debug = DEBUG_DEVELOPER;
    
    /*
     * Put the synch configs into their own object in the global configs
     * @var stdClass $CFG->synch
     */
    $CFG->synch = new stdClass;
    /*
     * Web path for the synch feature
     */
    $CFG->synch->wwwroot = $CFG->wwwroot.'/synch';
    /*
     * Synch related information is stored in the Moodle Data directory in its
     * own folder
     */
    $CFG->synch->dataroot = $CFG->dataroot.'/synch';
    /*
     * Queue Configs
     * The queue is resides in the Moodle data folder and is divided into two
     * folders: 
     * @	in:	files received from outside Moodle 
     * @	out: files ready to be transferred out of Moodle
     */
    $CFG->synch->path_queue_in = $CFG->dataroot.'/synch/queue/in';
    $CFG->synch->path_queue_out = $CFG->dataroot.'/synch/queue/out';
    

    /*
     * Individual session files are stored in the data folder
     */
    $CFG->synch->path_sessions = $CFG->synch->dataroot.'/sessions';
    
    /*
     * 
     */
    $CFG->synch->path_backups = '/backups';
    
    $CFG->synch->session_file_name = '/session.txt';
    $CFG->synch->course_shortname_default = 'default';
    $CFG->synch->merge_file_suffix = 'merge';
    
    /*
     * Setup some a simple debugging class that uses standard php echo and
     * print_r functionality but adds the file name and path and line number
     * from where the debugging occurs. Generally the debugging output is cached
     * and displayed at the end of the page to make it easier to read and so as
     * not to ruin the display.
     */
	GLOBAL $Out;
	$Out = new Out();
    /*
     * turn outputting on for the class. If this is off it will never output
     * anything. Useful if you need to stop the class outputting completely
     */
	$Out->setGlobalDisplay(true);
    /*
     * Stop and start recording at a page or method level. It is possible to
     * turn recording off at the start of a script and only turn it on at the
     * point(s) it is needed. 
     */
	$Out->setDisplay(true);
    
    /*
     * Examples of using the debugging class
     * @ 1) simply print out a variable or any string
     * @ 2) use print_r functionality to display a compex item
     * @ 3) flush the debugger stream to the screen in cases where the script
     * doesn't finish.
     */
     //$Out->append('$CFG->synch->session_file_name = '.$CFG->synch->session_file_name);
     //$Out->print_r($CFG, '$CFG = ');
     // $Out-flush();
     
    /*
     * Instantiate the controllers and make them available globally. 
     */
     
    // $SynchManager is the overall controller for the synch process.
    GLOBAL $SynchManager;
	$SynchManager = new synch_Synch_controller();
    $CFG->synch->synchManager = $SynchManager; 
	
    // $SynchViewController is concerned entirely with getting information to the page. 
    GLOBAL $SynchViewController;
	$SynchViewController = new synch_view_controller();
    
    // $SynchSessionController handles each synchronisation session. 
    GLOBAL $SynchSessionController;
    $SynchSessionController = new synch_session_controller();
    
    // $SynchServerController manages the list of trusted servers
    GLOBAL $SynchServerController;
    $SynchServerController = new synch_server_controller();
    
    /*
     * Define the action values to use when recording activity for later
     * synchronisation
     */
    define('SYNCH_ACTION_CREATE', 1);
    define('SYNCH_ACTION_READ', 2);
    define('SYNCH_ACTION_UPDATE', 3);
    define('SYNCH_ACTION_DELETE', 4);
	?>