<?php
/*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * Content Items store synch navigation details for an individual item
 */
 class SynchContentItem extends Synch_Modal{
 	
 	/*
 	protected $defaultProperties = array('id', 'name', 'description', 'parent', 'type');
 	*/
 	
 	function __construct($props=null){
 		$this->primaryKey = 'id';
        if(!is_array($props)){
        	$props = array();
        }
        
        if(!isset($props['serverIds']) || !is_array($props['serverIds'])){
        	$props['serverIds'] = array();
        }
 		parent::__construct($props);
 	}
    
    public function appendServerId($id){
    	$this->properties['serverIds'][] = $id;
    }
 }
?>
