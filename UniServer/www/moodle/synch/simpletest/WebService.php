<?php
/*
 * Created on 14 Aug 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 require_once(dirname(__FILE__) . '/../../config.php');
 
 class UnitTestWebService extends UnitTestCase{
 	
 	protected $wwwroot = null;
 	protected $peer = null;
 	protected $request = null;
 	protected $method = null;
 	protected $hostId = null;
 	protected $remoteRoot = null;
 	
 	function __construct() {
       parent::UnitTestCase();
       GLOBAL $CFG;
		
		// For the demo, our 'remote' host is actually our local host.
		$this->wwwroot = $CFG->wwwroot;
		$this->remoteRoot = $this->wwwroot;
    }
    
    function __destruct(){
    	global $Out; 
    	$Out->flush();
    }
    
    function setup(){
    	
    	
		GLOBAL $Out;
		//$Out->append('$this->wwwroot = '.$this->wwwroot);
		// mnet_peer pulls information about a remote host from the database.
		$this->peer = new mnet_peer();
		$this->peer->set_wwwroot($this->wwwroot);
		
		$this->method = 'synch/mnet/synch.php/testResponse';
		
		// Create a new request object
		$this->request = new mnet_xmlrpc_client();
		
		// Tell it the path to the method that we want to execute
		$this->request->set_method($this->method);
		if(!$this->getHostId()){
			$hostId = $this->getHostIdFromWwwRoot($this->wwwroot);
			//$Out->append('$hostId = '.$hostId);
			// Set host id for checking later
			$this->setHostId($hostId);
		}
    }
    
    function tearDown(){
    	global $Out; 
    	$Out->flush();
    }
    
    function call($paramArray = null){
		
	
		GLOBAL $Out;
		//$Out->print_r($paramArray, '$paramArray = ');
		//$Out->flush();
		if(!empty($paramArray)){
			// Add parameters for your function. The mnet_concatenate_strings takes three
			// parameters, like mnet_concatenate_strings($string1, $string2, $string3)
			// PHP is weakly typed, so you can get away with calling most things strings, 
			// unless it's non-scalar (i.e. an array or object or something).
			foreach($paramArray as $param) {
			    $this->request->add_param($param[0], $param[1]);
			}
		}
		
		//$Out->print_r($this->request->params, '$this->request->params = ');
		if (false && count($this->request->params)) {
		    $Out->append('Your parameters are:<br />');
		    while(list($key, $val) = each($this->request->params)) {
		        $Out->append('&nbsp;&nbsp; <strong>'.$key.':</strong> '. $val."<br/>\n");
		    }
		}
		
		if(!$this->getHostId()){
			$this->peer->set_id($this->getHostId());
		}
		// We send the request:
		$this->request->send($this->peer);
		//$Out->append('$this->request->response = '.$this->request->response);
		//$Out->flush();
		
		// The response contains the url of the service called. We can check this against 
		// the remoteRoot to ensure they match and thus prove the correct host was called.
		
		//$Out->print_r($this->request->response, '$this->request->response (1) = ');
		//$Out->flush();
		// Split the response into the actual response and the remoteRoot
		if(is_array($this->request->response)){
			$host = $this->request->response['host'];
			$this->assertIdenticalHost($host, $this->remoteRoot);
			$this->request->response = $this->request->response['response'];
			
		} 
		
		//$Out->print_r($this->request->response, '$this->request->response (2) = ');
		//$Out->flush();
    	return $this->request->response;
	}
	
	function assertIdenticalHost($host, $url){
		if(empty($host) || empty($host['wwwroot'])){
			return false;
		}
		
		$this->assertIdentical($host['wwwroot'], $url);
	}
    
    function getHostId(){
    	return $this->hostId;
    }
    
    function setHostId($new){
    	$this->hostId = $new;
    }
    
    function getHostIdFromWwwRoot($wwwroot=null){
    	if(empty($wwwroot)){
    		GLOBAL $CFG;
    		$wwwroot = $this->wwwroot;
    	}
    	
    	$id = get_field('mnet_host', 'id', 'wwwroot', $wwwroot);
    	
    	if(empty($id)){
    		return null;
    	}
    	
    	return $id;
    }
 }
?>
