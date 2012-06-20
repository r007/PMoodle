<?php
/*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * Synch data is a class used by the original proof of concept to store data of
 * current actions on the system such as creating, reading, updating or deleting
 * content
 */
class synch_data extends bean_synch_BaseClass{

	function __construct(){

		$this->data->id = null;
		$this->data->serverid = null;
		$this->data->moduleid = null;
		$this->data->tableid = null;
		$this->data->tablerowid = 0;
		$this->data->lastupdated = 0;
		$this->data->action = 0;
	}

	function getId(){
		return $this->data->id;
	}

	function setId($new=null){
			$this->data->id = $new;
	}

	function getServerId(){
		return $this->data->serverid;
	}

	function setServerId($new=null){
			$this->data->serverid = $new;
	}
	
	function getModuleId(){
		return $this->data->moduleid;
	}

	function setModuleId($new=null){
			$this->data->moduleid = $new;
	}

	function getTableId(){
		return $this->data->tableid;
	}

	function setTableId($new=null){
			$this->data->tableid = $new;
	}

	function getTableRowId(){
			return $this->data->tablerowid;
	}

	function setTableRowId($new=null){
				$this->data->tablerowid = $new;
	}

	function getLastUpdated(){
				return $this->data->lastupdated;
		}

	function setLastUpdated($new=null){
					$this->data->lastupdated = $new;
	}

	function getAction(){
			return $this->data->action;
	}

	function setAction($new=null){
				$this->data->action = $new;
	}


	function output($text='', $level=0){
		$level++;
		GLOBAL $Out;
		$Out->append("Outputting synch_Data", $level);

		$Out->append("id = " . $this->getId(), $level);
		$Out->append("serverId = " . $this->getServerId(), $level);
		$Out->append("moduleId = ".$this->getModuleId(), $level);
		$Out->append("tableId = ".$this->getTableId(), $level);
		$Out->append("tableRowId= ".$this->getTableRowId(), $level);
		$Out->append("lastUpdated = ".$this->getLastUpdated(), $level);
		$Out->append("action = ".$this->getAction(), $level);
	}
	
}

?>