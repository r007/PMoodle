<?php
/*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * This class provides easy access to and management of the configruation
 * details it contains. 
 */
 
 class synch_Configuration_controller {
 	
 	protected $configurations = array();
 	protected $className = "";	
 
 	 function createAndAppendConfiguration($name, $description){
      	$this->appendConfiguration($this->createConfiguration($name, $description));
    }
    
    function createConfiguration($name, $description){
	   	$className = $this->getClassName();
      	return new $className($name, $description);
    }
    
    function appendConfiguration($type){
    	$this->configurations[$type->getName()]=$type;
    }
    
    function removeConfiguration($type){
    	if(isset($this->configurations[$type->getName()])){
    		unset($this->configurations[$type->getName()]);
    	}
    }
    
    function getConfiguration($name){
    	if(!isset($this->configurations[$name])){
    		return null;
    	}
    	return $this->configurations[$name];
    }
    
    function setConfiguration($type){
    	$this->configurations[$type->getName()]=$type;
    }
    
    function getClassName(){
    	return $this->className;
    }
    
    function setClassName($new){
    	$this->className = $new;
    }
 }
?>
