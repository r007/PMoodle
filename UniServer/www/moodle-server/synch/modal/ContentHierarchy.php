<?php
/*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * Moodle contains content which is arranged int o a hierarchy of category >
 * course > module/activity/resource. This class stores and manipulates this
 * content
 */
 class SynchContentHierarchy{
 	
 	protected $data = array();
 	
 	/*
 	 * An array that records the parent child relationships of all data items.
 	 */
 	protected $hierarchy = array(); 
 	
 	/*
 	 * Tobe truly unique data item ids must contain both the id from the data
 	 * item and its type. 
 	 */
 	public function generateDataItemId($id, $type){
 		return $id.'-'.$type;
 	}
 	
 	/* Return just the id value from the Data Id value of id-type 
 	 * @param $dataId string
 	 * return string
 	 */
 	public static function getIdFromDataItemId($dataId){
 		if(!isset($dataId)){
 			return null;
 		}
 		
 		$contents = split('-',$dataId);
 		return $contents[0];
 	}
 	
 	/* Return just the type value from the Data Id value of id-type 
 	 * @param $dataId string
 	 * return string
 	 */
 	public static function getTypeFromDataItemId($dataId){
 		if(!isset($dataId)){
 			return null;
 		}
 		
 		$contents = split('-',$dataId);
 		return $contents[1];
 	}
 	
 	/*
 	 * Load an array of data into the data array
     * @param array $data SynchContentItem The data items to load
     * @param int $type The type of the data items
     * @param string $parentId DataItemId denoting the parent
     * @return bool
 	 */
 	public function loadData($data, $type, $parentId=null){
		
 		if(!isset($data)){
 			return false;
 		}
 		
 		if(!is_array($data)){
 			return $this->loadDataItem($data, $type, $parentId);
 		}
 		
 		foreach($data as $item){
 			if(!$this->loadDataItem($item, $type, $parentId)){
 				return false;
 			}
 		}
 		
 		return true;
 	} 
 	
 	/*
 	 * Load a dataitem into the data array
     * @param SynchContentItem $item
     * @param int $type The type of the data items
     * @param string $parentId DataItemId denoting the parent
     * @return bool
 	 */
 	public function loadDataItem($item, $typeId, $parentId=null){
 		if(!isset($item)){
 			return false;
 		}
 		
 		if(!isset($parentId)){
 			$parentId = 0;
 		}
 		
 		if(!isset($item->type)){
 			$item->typeId = $typeId;
 		}
 		
 		if(!isset($item->parentId)){
 			$item->parentId = $parentId;
 		}
        
 		$id = $this->generateDataItemId($item->id, $typeId);
        
        if(isset($this->data[$id])){
            $child = $this->data[$id];
            $child->appendServerId($item->sourceServerId);
        }
        else{
 		     $this->data[$id] = $item;
        }
 		$this->appendHierarchyItem($id, $parentId);
        
 		return true;
 	}
 	
 	/*
 	 * Add an item to the the hierarchy to record the parent child relationship
     * @param string $id DataItemId of the child 
     * @param string $parentId DataItemId of the parent
     * @return void
 	 */
 	public function appendHierarchyItem($id, $parentId){
 		
        // If the parentid is not present in the hierarchy already create a 
        // new array for it
 		if(!isset($this->hierarchy[$parentId])){
 			$this->hierarchy[$parentId] = array();
 		}
 		
 		$this->hierarchy[$parentId][] = $id;
 	}
 	
 	/*
 	 * Get the root items of the hierarchy
     * @return array SynchContentItem
 	 */
 	public function getRootItems(){
 		
 		return $this->getDataItemsByParentId(0);
 	}
 	
 	/*
 	 * Get the hierarchy as a nested array of SynchContentItems
 	 */
 	public function getHierarchy(){
 		$hierarchy = array();
 		$hierarchy = $this->buildHierarchy($hierarchy);
		return $hierarchy;
 	}
 	
 	/*
 	 * Get the hierarchy as a nested array of SynchContentItems
 	 */
 	public function buildHierarchy($child, $parentId=0){
 		
 		$childIds = $this->getChildIdsFromParentId($parentId);
 		if(!isset($childIds) || !is_array($childIds)){
 			return null;
 		}
 		
 		foreach($childIds as $id){
 			$child[$id] = $this->getDataItem($id);
 			$children = array();
			$child[$id]->children = $this->buildHierarchy($children, $id);
 		}
 		
 		return $child;
 	}
 	
 	/*
 	 * Return the the hierarchy data in serialised format
     * @return string serialized array containing the data and hierarchy arrays
 	 */
 	public function serializeHierarchyData(){
 		
 		// Get the data and hierarchy arrays and package into an array
 		$backup = array(
							'data' => $this->getData(),
							'hierarchy' => $this->getHierarchyData(),
						);
						
		return serialize($backup);
 		
 	}
 	
 	/*
 	 * Restore the hierarchy data from the cached version
     * @param string $cachedHierarchy serialized array containing the data and
     * hierarchy arrays
     * @return bool
 	 */
 	public function restoreHierarchyData($cachedHierarchy = null){
 		
 		if(!isset($cachedHierarchy)){
 			return false;
 		}
 		
 		$cachedHierarchy = unserialize($cachedHierarchy);
 		if(!is_array($cachedHierarchy) || !isset($cachedHierarchy['data'])
	    	|| !isset($cachedHierarchy['hierarchy'])){
	    	return false;
	    }
	    
 		$this->setData($cachedHierarchy['data']);
	    $this->setHierarchyData($cachedHierarchy['hierarchy']);
 	}
 	
 	/*
 	 * Get the hierarchy children SynchContentItems from the parent id passed
 	 * in 
     * @param string $parentId DataItemId 
     * @return array SynchContentItem
 	 */
 	public function getHierarchyItemsByParentId($parentId){
 		
 		if(!isset($parentId) || !$this->hierarchy[$parentId]){
 			return null;
 		}
 		
 		$hierarchy = $this->getDataItemsByParentId($parentId);
 	}
 	
 	/*
 	 * Get an array of child data item ids by the id of their parent
 	 * @param string $parentId Data Item Id
 	 * @return array data item ids
 	 */
 	public function getChildIdsFromParentId($parentId){

 		if(!isset($parentId) || !isset($this->hierarchy[$parentId])){
 			return null;
 		}
 		
 		return $this->hierarchy[$parentId];
 	}
 	
 	/*
 	 * Return the hierarchy array 
 	 * @return array ids
 	 */
 	public function getHierarchyData(){
 		return $this->hierarchy;
 	}
 	
 	/*
 	 * Set the hierarchy array
 	 * @param array $new
 	 * @return void
 	 */
 	public function setHierarchyData($new){
  		$this->hierarchy = $new;
 	}
 	
 	/*
 	 * Return the array of data items
 	 * @return array SynchContentItems
 	 */
 	public function getData(){
 		return $this->data;
 	}
 	
 	/*
 	 * Return the array of data items
 	 * @param array $new
 	 * @return void
 	 */
 	public function setData($new){
 		
 		$this->data = $new;
 	}
 	
 	/*
 	 * Get a data item by its id
 	 * @param string $id Data Item Id
 	 * @return object SynchContentItem
 	 */
 	public function getDataItem($id){
 		if(!isset($id) || !isset($this->data[$id])){
 			return null;
 		}
 		
 		return $this->data[$id];

 	}
 	
 	/*
 	 * Check if a data item is in the data array by its id
 	 * @param string $id Data Item Id
 	 * @return bool
 	 */
 	public function hasDataItem($id){
 		$item = $this->getDataItem($id);
 		return isset($item);

 	}
 	
 	/*
 	 * Get a hierarchy item by its id
 	 * @param string $id Data Item Id
 	 * @return array 
 	 */
 	public function getHierarchyItem($id){
 		if(!isset($id) || !isset($this->hierarchy[$id])){
 			return null;
 		}
 		
 		return $this->hierarchy[$id];

 	}
 	
 	/*
 	 * Check if an id is present at the root of the hierarchy array. This
 	 * reflects whether any children have yet been found for the item
 	 * @param string $id Data Item Id
 	 * @return bool
 	 */
 	public function hasHierarchyItem($id){
 		$item = $this->getHierarchyItem($id);
 		return isset($item);

 	}
 	
 	 /*
 	 * Check if a data item has loaded any modules.
 	 * @param string $id Data Item Id
 	 * @return bool
 	 */
 	public function hasModulesLoaded($parentDataItemId){
 		$item = $this->getDataItem($parentDataItemId);
 		
 		if(!isset($item) || !is_array($item->modules)){
 			return false;
 		}
 		
 		return true;
 	}
 	
 	/*
 	 * Get an array of data items by the id of their parent
 	 * @param string $parentId Data Item Id
 	 * @return array SynchContentItems
 	 */
 	public function getDataItemsByParentId($parentId){
 		if(!isset($parentId)){
 			return null;
 		}
 		
 		$childIds = $this->getChildIdsFromParentId($parentId);
 		
 		if(!isset($childIds) || !is_array($childIds)){
 			return null;
 		}
 		
 		return $this->getDataItems($childIds);
 		
 	}
 	
 	/*
 	 * Get an array of data items by their ids
 	 * @param array $ids  Data Item Ids
 	 * @return array SynchContentItems
 	 */
 	public function getDataItems($ids){
 		
 		$items = array();
 		foreach($ids as $id){
 			$items[] = $this->getDataItem($id);
 		}
 		return $items;
 	}
    
    /*
     * Get the id of a parent item in the hierarchy given the id of the current
     * item and the level to go up to.
     * @param string $currentId  Data Item Id
     * @return string
     */
    public function getParentId($currentId, $level=0){
    	$item = $this->getDataItem($currentId);
        
        if(!isset($item)){
        	return null;
        }
        
        $parentId = $item->parentId;
        if($level){
        	$level--;
            $this->getParentId($parentId, $level);
        }
        
        return $parentId;
    }
 	
 	/*
 	 * Output content from the data and hierarchy arrays using the $Out class
 	 */
 	public function outputData($text = 'Outputting Content Hierarchy', $level = 0){
 		GLOBAL $Out;
 		$level++;
 		$Out->append($text, $level);
 		$Out->print_r($this->data, '$this->data = ', $level);
 		$Out->print_r($this->hierarchy, '$this->hierarchy = ', $level);
 	}
 }
?>
