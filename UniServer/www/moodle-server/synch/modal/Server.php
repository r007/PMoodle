<?php
/*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * The synch process is underpinned by trusted servers. This class stores their
 * details.
 */
 
 class synch_modal_Server extends Synch_Modal{
 	
 	/*
 	protected $defaultProperties = array('id', 'name', 'description', 'parent', 'type');
 	*/
 	
    /*
     * There hasn't been time to figure out how best to manage what can and
     * cannot be added so the expected properties will just be listed for now.
     * 
     * The basic properties of a session object should be:
     * @param string id: Unique Session Id
     * @param string name: Name given by the user to the session. 
     */
 	function __construct($props=null){
 		$this->primaryKey = 'id';
 		parent::__construct($props);
 	}
 }
?>
