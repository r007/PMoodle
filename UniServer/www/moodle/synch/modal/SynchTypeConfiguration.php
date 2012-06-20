<?php
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
 
 class SynchTypeConfiguration {
 	
	protected	$name = "";
 	protected	$description = "";
	function __construct($name='', $description=''){
		$this->name = $name;
		$this->description = $description;
	}
	
	function getName(){
		return $this->name;
	}
	
	function setName($name){
		$this->name = $name;
	}
	
	function getDescription(){
		return $this->description;
	}
	
	function setDescription($description){
		$this->description = $description;
	}
 }
?>