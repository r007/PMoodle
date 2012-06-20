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
require_once(dirname(__FILE__) . '/../../config.php');
global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');

require_once dirname(__FILE__)."/../admin/synch-setup.php";
require_once $CFG->dirroot.'/mnet/xmlrpc/client.php';

class basicWebService_test extends UnitTestCase {

    var $courseid= 0;
    var $userid  = 0;
    var $userid_2= 0;
    var $groupid = 0;

    function __construct() {
       parent::UnitTestCase();
       
    }
    
    function __destruct(){
    	GLOBAL $Out; 
    	$Out->flush();
    }

	function call($paramArray = null){
		
		GLOBAL $CFG;
		// For the demo, our 'remote' host is actually our local host.
		$wwwroot = $CFG->wwwroot;
		
		// mnet_peer pulls information about a remote host from the database.
		$mnet_peer = new mnet_peer();
		$mnet_peer->set_wwwroot($wwwroot);
		//$mnethostid = 1010000003;
		//$mnethostid = 1010000001;
		//$mnet_peer->set_id($mnethostid);
		
		$method = 'synch/mnet/synch.php/testResponse';
		//$paramArray = array();
		
		// Create a new request object
		$mnet_request = new mnet_xmlrpc_client();
		
		// Tell it the path to the method that we want to execute
		$mnet_request->set_method($method);
		
		GLOBAL $Out;
		//$Out->print_r($paramArray, '$paramArray = ');
		if(!empty($paramArray)){
			// Add parameters for your function. The mnet_concatenate_strings takes three
			// parameters, like mnet_concatenate_strings($string1, $string2, $string3)
			// PHP is weakly typed, so you can get away with calling most things strings, 
			// unless it's non-scalar (i.e. an array or object or something).
			foreach($paramArray as $param) {
			    $mnet_request->add_param($param[0], $param[1]);
			}
		}
		
		//$Out->print_r($mnet_request->params, '$mnet_request->params = ');
		if (false && count($mnet_request->params)) {
		    $Out->append('Your parameters are:<br />');
		    while(list($key, $val) = each($mnet_request->params)) {
		        $Out->append('&nbsp;&nbsp; <strong>'.$key.':</strong> '. $val."<br/>\n");
		    }
		}
		
		
		
		// We send the request:
		$mnet_request->send($mnet_peer);
		//$Out->append('$mnet_request->response = '.$mnet_request->response);
		//$Out->flush();
    	return $mnet_request->response;
	}
	
    function test_create_synch_data() {
     //  $data = new synch_data();
      // echo("testing sycnh");
       $this->assertTrue(true);
    }
    
    function testGetString(){
    	
		$test = 'test string';
		
		$response = $this->call(array(array($test, 'string')));
		GLOBAL $Out;
		$Out->print_r($test, '$test = ');
		$Out->print_r($response, '$response = ');
		$Out->flush();
		$this->assertIdentical($response, $test);
    }

	function testGetArray(){
		$test = array('content'=>'test string', 
						'description'=> 'testing arrays can be passed'
					);
		
		$response = $this->call(array(array($test, 'array')));
		GLOBAL $Out;
		$Out->print_r($test, '$test = ');
		$Out->print_r($response, '$response = ');
		$Out->flush();
		$this->assertIdentical($response, $test);
	}
	
	function testGetMultipleParameters(){
		$test = array('content'=>'test string', 
						'description'=> 'testing arrays can be passed'
					);
		$test2 = 'this string should appear last';
		$response = $this->call(array(
										array($test, 'array'),
										array($test2, 'string')
									)
								);
		GLOBAL $Out;
		$Out->print_r($test, '$test = ');
		$Out->print_r($test2, '$test2 = ');
		$Out->print_r($response, '$response = ');
		$Out->flush();
		$this->assertIdentical($response[0], $test);
		$this->assertIdentical($response[1], $test2);
	}
	
}

?>