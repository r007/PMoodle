<?php
/*
 *
 * @copyright &copy; 2006 The Open University
 * @author d.t.le@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * Web services implementation using Moodle Network. This is read by Moodle to
 * find out which methods to expose. 
 * 
 * Note: $CFG->debug = DEBUG_NONE; Any extra text added to the response can
 * invalidate it. Debugging errors are just this kind of text and so they
 * must be turned off. 
 * 
 */
 
 if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

//require_once($CFG->libdir.'/authlib.php');
GLOBAL $CFG;
    require_once(dirname(__FILE__).'/service.php');

    
/**
 * Moodle Network authentication plugin.
 */
class synch_plugin_mnet {

    /**
     * Constructor.
     */
    function __construct() {
        /*
        $this->authtype = 'mnet';
        $this->config = get_config('auth/mnet');
        */
    }

    /**
     * Provides the allowed RPC services from this class as an array.
     * @return array  Allowed RPC services.
     */
    function mnet_publishes() {

        $sso_idp = array();
        $sso_idp['name']        = 'synch_test'; // Name & Description go in lang file
        $sso_idp['apiversion']  = 1;
        /*
         * @var array $sso_idp['methods'] a list defining methods exposed by the
         * synch mnet plugin
         */
        $sso_idp['methods']     = array('test', 'testResponse', 'getBackupById', 
                                            'restoreMergedBackupFromRemoteHost', 'getServerDetails',
                                            'getHierarchyChildrenByIdAndType', 'getRemoteDetailsByDataItemId');


        return array($sso_idp);
    }
    
    function test(){
    	/*
    	// Testing which objects can be passed via the interface. 
    	$testObject = new Object();
    	$testObject->test = 'just testing what can be passed';
    	$testObject->testArray = Array('this','is','a','test');
    	$testObject->testObject = new Object();
    	$testObject->testObject->field = 'just a field';
    	$testObject->testsynch_plugin_mnet =  new synch_plugin_mnet();
    	$testObject->testDouble = 56.56;
    	$testObject->testInt = 56;
    	return $testObject;
    	*/
    	/* 
    	 // Testing that 1000 records can be passed via the web service interface
    	$testRecordSet = array();
    	$record;
    	for($i=0;$i<1000;$i++){
    		$record = new Object();
    		$record->name = 'My name is '.$i;
    		$record->id = '314325415345'.$i;
    		$record->table = 'mdl_some_table_here';
    		$testRecordSet[] = $record;
    	}
    	
    	return $testRecordSet;
    	*/
    	
    	// testing the characters that can be passed 
    	$testCharacters = "!\"£$%^&*()_+-={}[]:@~;'#<>?,./|\\¬`";
    	return $testCharacters;
    }
    
    /*
     * @method testResponse designed for unit testing to return the paramater
     * provided. Tests the web service data transfer
     * @param mixed $input value passed into the method
     * @return mixed
     */
    function testResponse($input, $input2=null){
    	if($input2){
    		$input = array($input, $input2);
    	}
        global $CFG;
    	return array('response'=>$input, 'host'=>array('wwwroot'=>$CFG->wwwroot));
    }
    
     /* @method getBackupById generates a backup from the id given and passes it back to the calling application
      * @param string $id id of item to backup
      * @param string $sessionId associates the request with a given session
     * @return mixed
     */
    public function getBackupById($id, $hostUrl, $sessionId){
    	
        $fileName = Synch_retrieveBackupById($id, $hostUrl, $sessionId);
        return $fileName;

    }
    
    
     /* @method restoreMergedBackupOnRemoteHost designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @param string $id courseid to backup
     * @return mixed
     */
    public function restoreMergedBackupFromRemoteHost($id, $hostUrl, $sessionId, $fileName){
        global $CFG;
        $CFG->debug = DEBUG_NONE;
        $success = Synch_restoreMergedBackupFromRemoteHost($id, $hostUrl, $sessionId, $fileName);
        return $success;

    }
    
     /* @method getServerDetails designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @param string $id courseid to backup
     * @return mixed
     */
    public function getServerDetails(){
        global $CFG;
        $CFG->debug = DEBUG_NONE;
        $details = Synch_getServerDetails();
        return $details;

    }
    
    /* @method getHierarchyChildrenByIdAndType get the children for a specific hierarchy item.
     * @param int id parent id
     * @param int type 
     * @return array SynchContentItem
     */
    public function getHierarchyChildrenByIdAndType($id, $type){
        global $CFG;
        $CFG->debug = DEBUG_NONE;
        $details = Synch_getHierarchyChildrenByIdAndType($id, $type);
        return $details;

    }
    
     /* @method getServerDetails designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @param string $id courseid to backup
     * @return mixed
     */
    public function getRemoteDetailsByDataItemId($dataItemId){
        global $CFG;
        $CFG->debug = DEBUG_NONE;
        $details = Synch_getDetailsByDataItemId($dataItemId);
        return $details;

    }
}
?>
