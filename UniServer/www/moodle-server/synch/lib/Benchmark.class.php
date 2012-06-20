<?php
 /*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * This is a simple class providing benchmarking methods 
 */
class Benchmark {
	
	protected $start = 0;
	protected $end = 0;
	
    function __construct() {
    }
    
    // benchmark timing
	function getmicrotime($t) {
		list($usec, $sec) = explode(" ",$t);
		return ((float)$usec + (float)$sec);
	}
	
	function start(){
		$this->start = microtime();
	}
	
	function end(){
		$this->end = microtime();
	}
	
	function reset(){
		$this->start = 0;
		$this->end = 0;
	}
	
	/*
	 * @return time in seconds
	 */
	function getResult(){
		return ($this->getmicrotime($this->end) - $this->getmicrotime($this->start));
	}
}
?>