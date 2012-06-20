<?php
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
 
    function synch_empty($value){
        return empty($value);	
    }
    
    function synch_isset($value){
        return isset($value);   
    }
    
    function synch_print_value($value, $default=''){
    	if(!isset($value)){
    		echo $default;
    	}
        
        echo $value;
    }
    
	function synch_RetrieveRecordById($id, $table){

		return get_record($table, 'id',$id);

	}

	function synch_GetRequestItem($field){

		if(isset($_GET[$field])){
			return $_GET[$field];
		}
		else if (isset($_POST[$field])){
			return $_POST[$field];
		}

		return null;
	}

	function synch_connect_to_database($details){
		
		GLOBAL $CFG;
		$db = &ADONewConnection($CFG->dbtype);
		
		// See MDL-6760 for why this is necessary. In Moodle 1.8, once we start using NULLs properly,
	    // we probably want to change this value to ''.
	    //$db->null2null = 'A long random string that will never, ever match something we want to insert into the database, I hope. \'';
	
	    if (!isset($CFG->dbpersist) or !empty($CFG->dbpersist)) {    // Use persistent connection (default)
	        $dbconnected = $db->PConnect($details->host,$details->username,$details->password,$details->name);
	    } else {                                                     // Use single connection
	        $dbconnected = $db->Connect($details->host,$details->username,$details->password,$details->name);
	    }
	    if (! $dbconnected) {
	        // In the name of protocol correctness, monitoring and performance
	        // profiling, set the appropriate error headers for machine comsumption
	        if (isset($_SERVER['SERVER_PROTOCOL'])) { 
	            // Avoid it with cron.php. Note that we assume it's HTTP/1.x
	            header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');        
	        }
	        // and then for human consumption...
	        echo '<html><body>';
	        echo '<table align="center"><tr>';
	        echo '<td style="color:#990000; text-align:center; font-size:large; border-width:1px; '.
	             '    border-color:#000000; border-style:solid; border-radius: 20px; border-collapse: collapse; '.
	             '    -moz-border-radius: 20px; padding: 15px">';
	        echo '<p>Error: Database connection failed.</p>';
	        echo '<p>It is possible that the database is overloaded or otherwise not running properly.</p>';
	        echo '<p>The site administrator should also check that the database details have been correctly specified in config.php</p>';
	        echo '</td></tr></table>';
	        echo '</body></html>';
	
	        if (!empty($CFG->emailconnectionerrorsto)) {
	            mail($CFG->emailconnectionerrorsto, 
	                 'WARNING: Database connection error: '.$CFG->wwwroot, 
	                 'Connection error: '.$CFG->wwwroot);
	        }
	        die;
	    }

	    return $db;
	}
	
	function synch_set_database($database){
		GLOBAL $db;
		$db = $database;	
	
	}
	
	function synch_set_default_database(){
		GLOBAL $CFG;
		global $Out;
		//$Out->type($CFG->synch->databases->client->instance, "\$CFG->synch->databases->client->instance = .");
		synch_set_database($CFG->synch->databases->client->instance);
	
	}
    
    function synch_get_http_prefix(){
    	global $CFG;
        // If we're on a secure page ensure all urls are secure to prevent browser error
        if(empty($CFG->loginhttps)) {
            return $CFG->wwwroot;
        } 
        
        return str_replace('http:','https:',$CFG->wwwroot);
    }
    
?>