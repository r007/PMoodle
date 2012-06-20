<?php
/**
 * Unit tests for new Moodle Groups - basicgrouplib.php and some of utillib.php.
 * 
 * /admin/report/simpletest/index.php?showpasses=1&showsearch=1&path=course%2Fgroups
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 *
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once dirname(__FILE__)."/test_localWebService.php";

class synchRemoteWebService_test extends synchWebService_test {

		// The intention is to test the web service on the remove client. This test isn't working
	// as yet 2007 08 14. the idea is to write a standard suite of tests which can be run against
	// the local client and the remote to check the web service at both ends. 
	
	// The local tests are working fine. Connecting to the remote host is proving problematic. It could
	// simply be that I need to build this class from the ground up since the local tests are working fine. 
	// Or it could be the web service is throwing an error that we don't catch.   
	// cc5983
	
    function __construct() {
       parent::UnitTestCase();
       
    }
    
    function __destruct(){
    	GLOBAL $Out; 
    	$Out->flush();
    }

	function call($paramArray = null){
		
		/*
		GLOBAL $CFG;
		// For the demo, our 'remote' host is actually our local host.
		$wwwroot = $CFG->wwwroot;
		
		// mnet_peer pulls information about a remote host from the database.
		$mnet_peer = new mnet_peer();
		$mnet_peer->set_wwwroot($wwwroot);
		$mnethostid = 1010000004;
		//$mnethostid = 1010000001;
		$mnet_peer->set_id($mnethostid);
		
		*/
		
		global $Out;
		
		//$wwwroot = 'http://pclt1048.open.ac.uk/offline/development/client-1.9';
		//if(!$this->getHostId()){
			//$hostId = $this->getHostIdFromWwwRoot($wwwroot);
			//$Out->append('$hostId = '.$hostId);
			// Set host id for checking later
			//$this->setHostId($hostId);
		//}
		
		$Out->flush();
		parent::call($paramArray);
		
		
	}
	
		/*
	 * Can a string be passed back and forth without being corrupted.
	 */
    function testGetString(){
    	
		parent::testGetString();
    }

	/*
	 * Can a small array be passed back and forth without being corrupted.
	 */
	function testGetArray(){
		parent::testGetArray();
	}
	
	/*
	 * Can multiple parameters be passed back and forth without being corrupted.
	 */
	function testGetMultipleParameters(){
		parent::testGetMultipleParameters();
	}
    

	
}

?>