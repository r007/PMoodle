<?php
/*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 *  Session Items store details for the item being synchronised for a particular
 * session
 */
 class SynchSessionItem extends Synch_Modal{
 	
 	/*
 	protected $defaultProperties = array('id', 'name', 'description', 'parent', 'type');
 	*/
 	
 	function __construct($props=null){
 		$this->primaryKey = 'id';
        if(!is_array($props)){
        	$props = array(
                            'id'=>null,
                            'title'=>null,
                            'type'=>null,
                            'summary'=>null,
                            'lastSynched'=>null
                            );
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
