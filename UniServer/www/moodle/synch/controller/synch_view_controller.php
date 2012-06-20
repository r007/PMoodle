<?php
 /*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * This  controller provides reusable methods to output information content
 * formatted for a specific medium.
 */
 
 require_once dirname(__FILE__)."/../lib/FileSystem.php";
 
 define('MDL_NEW_LINE', chr(10));
 define('MDL_TAB', chr(9));
 class synch_view_controller{
 
 	protected $dataRoot = '/synch';
 	protected $serialisedNavFileName = 'serialised_navigation.txt';
 	
 	/*
 	 * Content Type ids are used to distinguish similar items from each other
 	 * and determine their level in the hierarchy
 	 */
 	public static $TYPE_ID_HUB = 1;
 	public static $TYPE_ID_CATEGORY = 2;
 	public static $TYPE_ID_COURSE = 3;
 	public static $TYPE_ID_SECTION = 4;
 	public static $TYPE_ID_MODULE = 5;
 	public static $TYPE_ID_FORUM = 6;
 	
 	/*
 	 * Content Types are used to distinguish similar items from each other and
 	 * determine their level in the hierarchy
 	 */
 	public static $TYPE_HUB = 'hub';
 	public static $TYPE_CATEGORY = 'category';
 	public static $TYPE_COURSE = 'course';
 	public static $TYPE_SECTION = 'section';
 	public static $TYPE_MODULE = 'module';
 	public static $TYPE_FORUM = 'forum';
 	
	
 	/*
 	 * Returns a a path to the synch folder inside $CFG->dataroot
 	 * @uses $CFG
 	 * @return string
 	 */
 	public function getDataRoot(){
 		GLOBAL $CFG;
 		return $CFG->dataroot.$this->dataRoot;	
 	} 
 	
 	/*
 	 * Returns a a path to the synch folder inside $CFG->dataroot
 	 * @uses $CFG
 	 */
 	public function getSerialisedNavFileName(){
 		return $this->serialisedNavFileName;
 	}
 	
 	/*
 	 * Prepare the hierarchy. Check the cached data is loaded and the request
 	 * items are retrieved and loaded.
     * @param string $id 
     * @return bool
 	 */
 	public function prepareHierarchy($id, $remoteServerId=null){
 		
 		GLOBAL $SynchContentHierarchy;

        global $Out;
        GLOBAL $SynchContentHierarchy;
        
        $hierarchy = $SynchContentHierarchy->getHierarchyData();
  //      $Out->print_r($hierarchy, '$hierarchy = ');
        $data = $SynchContentHierarchy->getData();
//        $Out->print_r($data, '$data = ');
 		//check if the root node is there. If not load it. 
		if(!$SynchContentHierarchy->hasHierarchyItem(0)){
			$this->loadChildrenByIdAndType(null, synch_view_controller::$TYPE_ID_HUB, $remoteServerId);
		}
		
		if(!isset($id) || $id==0){
			return true;
		}
		
		//Then if an id was passed check that it is loaded. If not load it.
		$type = SynchContentHierarchy::getTypeFromDataItemId($id)+1;
		
		if($SynchContentHierarchy->hasHierarchyItem($id)){
			return true;
		}
		
		$this->loadChildrenByIdAndType($id, $type, $remoteServerId);
		
        $hierarchy = $SynchContentHierarchy->getHierarchyData();
      //  $Out->print_r($hierarchy, '$hierarchy (2) = ');
        $data = $SynchContentHierarchy->getData();
    //    $Out->print_r($data, '$data (2) = ');
        return true;
 	}
 	
    /*
     * Return a list of available courses converted to ContentItem objects.
     * @uses $CFG
     * @param string $parentId Id of the Category to retrieve courses from 
     * @return array SynchContentItem
     */
    public function convertRecordsToContentItems($records, $fields){
        
        GLOBAL $Out;
        if(!$records || !count($records)){
            return null;
        }
        
        $contentItems = array();
        $contentItem = null;
        $record = null;
        
//        $Out->print_r($fields, '$fields = ');
        foreach($records as $record){
            $contentItem = new SynchContentItem(
                                        array(
                                            'id'=>$record->{$fields[0]},
                                            'name'=>$record->{$fields[1]},
                                            'description'=>$record->{$fields[2]},
                                        )
                                    );
            $contentItems[$record->id] = $contentItem;
        }
        
        GLOBAL $Out;
        //$Out->print_r($contentItems, '$contentItems = ');

        return $contentItems;
    }
    
 	/*
 	 * Return a list of available Hubs converted to ContentItem objects.
     * @return array SynchContentItem
 	 */
 	public function getHubsForHierarchy(){
 		
        global $CFG;
 		$contentItems = array();
 		$contentItem = null;
 		$category = null;
 		
        // Get the server details 
        $hosts = get_records_sql('  SELECT 
                                    s.id,
                                    s.description, 
                                    s.serverId,
                                    h.id as mnetHostId, 
                                    h.wwwroot, 
                                    h.name
                                FROM  
                                    '.$CFG->prefix.'mnet_host h,  
                                    '.$CFG->prefix.'synch_servers s  
                                WHERE 
                                    h.deleted = \'0\' AND
                                    s.mnetHostId = h.id');

        if (empty($hosts)){ 
            $hosts = array();
        }
        
        foreach($hosts as $host) {
        	$contentItem = new SynchContentItem(
                                            array(
                                                'id'=>$host->serverId,
                                                'name'=>$host->name,
                                                'description'=>$host->description,
                                            )
                                        );
            $contentItems[$contentItem->id] = $contentItem;
        }
    
 		return $contentItems;
 	}
 	
 	/*
 	 * Return a list of available categories converted to ContentItem objects.
     * @param string $parentId Id of the Hub to retrieve categories from 
     * @return array SynchContentItem
 	 */
 	public function getCategoriesForHierarchy($parentId){
 
        // Get the records from the database 
        // TODO: $parentId (hubId) needs to be tied to the retrieval of categories 
 		$records = get_categories();
        
        // Define the fields to retrieve content from. 
        $fields = array('id', 'name', 'description');
        
        // Convert to a standardised array of SynchContentItems
        $contentItems = $this->convertRecordsToContentItems($records, $fields);
        
 		return $contentItems;
 	}

 
     	
 	/*
 	 * Return a list of available courses converted to ContentItem objects.
     * @uses $CFG
 	 * @param string $parentId Id of the Category to retrieve courses from 
     * @return array SynchContentItem
 	 */
 	public function getCoursesForHierarchy($parentId){
 		GLOBAL $CFG, $SynchContentHierarchy;
 		
        // Get the records from the database 
 		$categoryid = SynchContentHierarchy::getIdFromDataItemId($parentId);
 		$sort="c.sortorder ASC";
 		$sql = "SELECT * FROM {$CFG->prefix}course c WHERE c.category = '$categoryid' ORDER BY $sort";
 		$records = get_records_sql($sql);

        // Define the fields to retrieve content from. 
        $fields = array('id', 'fullname', 'summary');
        
        // Convert to a standardised array of SynchContentItems
        $contentItems = $this->convertRecordsToContentItems($records, $fields);
 		
 		return $contentItems;
 	}
    
 	/*
 	 * Return a list of available sections converted to ContentItem objects.
     * @uses $CFG
 	 * @param string $parentId
     * @return array SynchContentItem
 	 */
 	public function getSectionsForHierarchy($parentDataItemId){
 		GLOBAL $CFG, $SynchContentHierarchy;
 		
        // Get the records from the database 
 		$parentId = SynchContentHierarchy::getIdFromDataItemId($parentDataItemId);
 		$records = get_records("course_sections", "course", "$parentId", "section",
                       "section, id, course, summary, sequence, visible");

        // Define the fields to retrieve content from. 
        $fields = array('id', 'section', 'summary');
        
        // Convert to a standardised array of SynchContentItems
        $contentItems = $this->convertRecordsToContentItems($records, $fields);
        
 		return $contentItems;
 	}
 	
 	/*
 	 * Append a list of available modules to its parent section.
     * @uses $CFG
 	 * @param string $parentId
 	 * @return array SynchContentItem
 	 */
 	public function getModulesForHierarchy($parentDataItemId){
 		
 		// Modules come from varied locations. We need to get the ids 
 		// of the modules in the current section. Then Get the actual module 
 		// records themselves
 		
 		GLOBAL $CFG;
 		GLOBAL $SynchContentHierarchy;
 		$parentId = SynchContentHierarchy::getIdFromDataItemId($parentDataItemId);
        
        // get the course id
        $courseDataItemId = $SynchContentHierarchy->getParentId($parentDataItemId, 1);
        $courseId = SynchContentHierarchy::getIdFromDataItemId($courseDataItemId);
        $sql = "SELECT * FROM {$CFG->prefix}course_modules cm WHERE cm.course='$courseId' AND cm.section = '$parentId'";
        $modules = get_records_sql($sql);
        
 		if(!$modules || !count($modules)){
 			return null;
 		}
 		
        $contentItems = array();
        $contentItem = null;
        
 		foreach($modules as $module){
 			$contentItem = $this->getModuleItemByModule($module);
            if(isset($contentItem)){
                $contentItems[$contentItem->id] = $contentItem;
            }
 		}
 		
        return $contentItems;

 	}
 	
 	/*
 	 * Return an array of module items using the information from the module
 	 * record passed in.
 	 * @param array $module
 	 * @return array
 	 */
 	public function getModuleItemByModule($module){
 		if(!isset($module) || !isset($module->module)){
 			return null;
 		}
 		GLOBAL $SynchContentHierarchy;
 		switch($module->module){
 			case '1010000006': // Forum
                 $dataItemId = $SynchContentHierarchy->generateDataItemId($module->instance, self::$TYPE_ID_FORUM);
                
                // Is the item already loaded
                if($SynchContentHierarchy->hasDataItem($dataItemId)){
                    return null;  // already loaded
                }
                return $this->getForumForHierarchy($module->instance);
	 			break;	
 		}
 	}
 	
 	/*
 	 * Get a list of instance ids from a data item by module type.
 	 * @param string $parentId
     * @return array ids
 	 */
 	public function getInstanceIdsFromModulesByModuleType($parentDataItemId, $type){
 		GLOBAL $SynchContentHierarchy;
 		$parent = $SynchContentHierarchy->getDataItem($parentDataItemId);
 		if(!is_array($parent->modules)){
 			return null;
 		}
 		
 		$instanceIds = array();
 		foreach($parent->modules as $module){
 			if($type == $module->module){
 				$instanceIds[] = $module->instance;
 			}
 		}
 		
 		return $instanceIds;
 	}
 	
 	/*
 	 * Get a list of instance ids from a data item by module type.
 	 * @param string $parentId
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
 	 * Return the forum identified by its id and converted to a ContentItem
 	 * object.
     * @uses $CFG
 	 * @param string $id instance id of forum
     * @return SynchContentItem
 	 */
 	public function getForumForHierarchy($id){
 		GLOBAL $CFG, $SynchContentHierarchy;
 		
        if(!isset($id)){
        	return null;
        }
        
 		$record = get_record("forum", "id", $id, null,
                       "id, course, type, name, intro");

 		if(!isset($record)){
 			return null;
 		}
 		
 		$contentItems = array();
		$contentItem = new SynchContentItem(
									array(
										'id'=>$record->id,
										'name'=>$record->name,
										'description'=>$record->intro,
									)
								);
                                
        // The hierarchy doesn't recognise individual forum types for now 2007 09 14 so a 
        // sub type is created instead to record the type id of the forum for use later                                  
        $contentItem->subTypeId = self::$TYPE_ID_FORUM;
        return $contentItem;

 	}
 	
 	/*
 	 * Return a list of available forums converted to ContentItem objects.
     * @uses $CFG
 	 * @param string $parentId
     * @return array SynchContentItem
 	 */
 	public function getForumsForHierarchy($parentDataItemId){
 		GLOBAL $CFG;
 		GLOBAL $SynchContentHierarchy;
 		
 		// Have the modules been loaded for the parent section? if not load them
 		if(!$SynchContentHierarchy->hasModulesLoaded($parentDataItemId)){
 		
 			$this->getAndAppendModulesToSection($parentDataItemId);
 		} 
 		
 		$parentId = SynchContentHierarchy::getIdFromDataItemId($parentDataItemId);
 		$sectionId = $parentId;
 		$forumIds = $this->getInstanceIdsFromModulesByModuleType($parentDataItemId, '1010000006');
 		
 		if(!is_array($forumIds)){
 			return null;
 		}
 		$records = get_records_list("forum", "id", implode(',', $forumIds), null,
                       "id, course, type, name, intro");

 		if(!$records || !count($records)){
 			return null;
 		}
 		
 		$contentItems = array();
 		$contentItem = null;
 		$record = null;
 		
 		foreach($records as $record){
 			$contentItem = new SynchContentItem(
 										array(
 											'id'=>$record->id,
 											'name'=>$record->name,
 											'description'=>$record->intro,
 										)
 									);
 			$contentItems[$record->id] = $contentItem;
 		}

 		return $contentItems;
 	}
 	
    /*
     * Load children into the hierarchy by their id and type
     * @param int id parent id
     * @param int type 
     * return bool
     */
    public function getChildrenByIdAndType($id, $type){
    	$children = null;
        switch($type){
            case self::$TYPE_ID_HUB:
                $children = $this->getHubsForHierarchy();
                break;
                
            case self::$TYPE_ID_CATEGORY:
                $children = $this->getCategoriesForHierarchy($id);
                break;
                
            case self::$TYPE_ID_COURSE:
                $children = $this->getCoursesForHierarchy($id);
                break;
                
            case self::$TYPE_ID_SECTION:
                $children = $this->getSectionsForHierarchy($id);
                break;
                
            case self::$TYPE_ID_MODULE:
                $children = $this->getModulesForHierarchy($id);
                break;
            
        }
        
        global $SynchServerController;
        $serverId = $SynchServerController->getServerId();
        // add the server id to each child
        if(is_array($children)){
             foreach($children as $child){   
                   $child->sourceServerId = $serverId;
                   $child->appendServerId($serverId);
             }
        }
        
        return $children;
    }
    
 	/*
 	 * Load children into the hierarchy by their id and type
 	 * @param int id parent id
 	 * @param int type 
 	 * return bool
 	 */
 	public function loadChildrenByIdAndType($id, $type, $remoteServerId=null){
 		
        global $SynchContentHierarchy;
        $children = array();
        
        // Get the children locally 
 		$localChildren = $this->getChildrenByIdAndType($id, $type);
        
        $children = $this->mergeChildren($children, $localChildren);
        
        global $Out;
        if($type != self::$TYPE_ID_HUB){
            // Get the children from a remote instance
            global $SynchManager;                   
     		$remoteChildren = $SynchManager->getRemoteHierarchyChildrenByIdAndType($remoteServerId, $id, $type);
            $children = $this->mergeChildren($children, $remoteChildren);
        }
        //$Out->print_r($children, '$children (2) = ');
 		GLOBAL $SynchContentHierarchy;
 		$SynchContentHierarchy->loadData($children, $type, $id);
 	}
    
    /*
     * Merge two arrays of children into one array. At this point just the
     * serverIds need to be merged. So if the child is already in the source
     * array just append the serverId of the comparison array
     */
    public function mergeChildren($source, $comparison){
    	
        $children = array();
        
        if((!isset($source) || !is_array($source)) 
            &&  (!isset($comparison) || !is_array($comparison)) 
            ){
            	
        	return $children;
        }

        if(!isset($source) || !is_array($source)){
        	return $comparison;
        }
        
        if(!isset($comparison) || !is_array($comparison)){
            return $source;
        }
        
        $children = $source;
        $childIds = array();
        foreach($children as $child){
            $children[$child->id] = $child;
            $childIds[] = $child->id;
        }

        foreach($comparison as $child){
            if(in_array($child->id, $childIds)){
                $child = $children[$child->id];
                $child->appendServerId($child->sourceServerId);
                continue;
            }
            $children[$child->id] = $child;
        }
        
        return $children;
    }
 	
 	/*
 	 * Write the synch navigation hierarchy from the hierarchy passed in.
 	 */
 	public function writeNavigationHierarchy($hierarchyController, $remoteServerId=null){
 		GLOBAL $Out;
 		if(!isset($hierarchyController)){
 			return null;
 		}
 		
 		// Get the hierarchy as a nested array of objects
  		$hierarchy = $hierarchyController->getHierarchy();
 		$indent = 7;
 		//$Out->print_r($hierarchy, '$hierarchy = ');
        
        
 		
        $baseTabs = $this->createTabString($indent);
 
        // Write the root list container
        echo MDL_NEW_LINE.$baseTabs .'<ul id="courseTree-0-list" class="root">'.MDL_NEW_LINE;
        if(isset($hierarchy) && is_array($hierarchy)){    
     		foreach($hierarchy as $item){
     			$this->writeNavigationListItem($item, $hierarchyController, $indent+1, $remoteServerId);
                global $Out;
                //$Out->append('$remoteServerId (1) = '.$remoteServerId);
     		}
        }
        
        // Write the end of the root list container
        echo MDL_NEW_LINE.$baseTabs .'</ul>'.MDL_NEW_LINE;
 		
 		// Write necessary javascript objects
 		//Write treeData array which is the content hierarchy data in javascript
 		$this->writeHierarchyTreeData($hierarchyController);
 		
 	}
 	
 	/*
 	 * Write the javascript tree data object using the content hierarchy data.
 	 */
 	public function writeHierarchyTreeData($hierarchyController){
 		
 		echo '<script  type="text/javascript" defer="defer">'.MDL_NEW_LINE;
 		
 		$data = $hierarchyController->getData();
 		echo MDL_TAB.'var treeData = {'.MDL_NEW_LINE;
 		if(is_array($data)){
	 		$size = count($data);
	 		$count = 0;
	 		foreach($data as $dataItemId => $item){
	 			$count++;
	 			$type = $this->getTypeByTypeId($item->typeId);
	 			$separator = $count<$size?',':'';
	 			echo MDL_TAB.MDL_TAB.'"'.$dataItemId.'": new TreeNode("'.$dataItemId.'", "'.$item->name.'", "'.$item->parentId.'", "'.$type.'")'.$separator.MDL_NEW_LINE;
	 			
	 		}
	 	}
 		echo MDL_TAB.'}'.MDL_NEW_LINE;
 		
 		$parentToChild = $hierarchyController->getHierarchyData();
 		echo MDL_TAB.'var parentToChild = {'.MDL_NEW_LINE;
 		if(is_array($parentToChild)){
	 		$size = count($parentToChild);
	 		$count = 0;
	 		foreach($parentToChild as $parentId =>$childIds){
				$count++;
				$separator = $count<$size?',':'';
				$childIdsString = $this->convertArrayToJavascriptArrayString($childIds); 
				echo MDL_TAB.MDL_TAB.'"'.$parentId.'"'.':['.$childIdsString.']'.$separator.MDL_NEW_LINE;
	 		}
 		}
		echo MDL_TAB.'}'.MDL_NEW_LINE;
 		echo '</script>'.MDL_NEW_LINE;
 	}
 	
 	/*
 	 * Write an item expand icon in the synch navigation list from the item
 	 * passed in. If it has no children write a heyperlink around it otherwise
 	 * write some javascript to trigger the method tree_toggleBranch
     * @uses $CFG
     * @param SynchContentItem $item
     * @param SynchContentHierarchy $hierarchyController
     * @return string html source code 
 	 */
 	public function writeNavigationListItemExpandIcon($item, $hierarchyController, $remoteServerId){
 		
        GLOBAL $CFG;
        
        $hasChildren = is_array($item->children);
 		$dataItemId = $hierarchyController->generateDataItemId($item->id, $item->typeId);
 		$source = '';
 		
 		if(!$hasChildren){
 			$source = '<a href="'.$CFG->synch->wwwroot.'/find_content.php?id='.urlencode($dataItemId);
            if(synch_isset($remoteServerId)){
                $source.='&amp;remoteServerId='.$remoteServerId;
            }
            $source.='">';
 		}
 		
        if(!$hasChildren){
        	$expandIcon = 'plus.gif';
        }
        else{
        	$expandIcon = 'minus.gif';
        }
 		$source .= '<img src="'.$CFG->synch->wwwroot.'/view/images/'.$expandIcon.'" id="courseTree-'.$dataItemId.'-icon-collapse" ';
 		if($hasChildren){
 			$source .='onclick="javascript: tree_toggleBranch(\''.$dataItemId.'\')"';
 		}
 		$source .='/>';
 		if(!$hasChildren){
 			$source .= '</a>';
 		}
 		return $source;
 	}
 	
 	/*
 	 * Write an item in the synch navigation list from the item passed in.
     * @uses $CFG
     * @param SynchContentItem $item
     * @param SynchContentHierarchy $hierarchyController
     * @param int $indent number of tabs to add indent the code to in the html
     * source. Just to tidy up the output and make it easier to debug.
     * @return void
 	 */
 	public function writeNavigationListItem($item, $hierarchyController, $indent=0, $remoteServerId=null){
 		
 		GLOBAL $CFG, $SynchServerController;

         // What is the remote serverId?
        if(empty($remoteServerId) && self::$TYPE_ID_HUB == $item->typeId){
            $remoteServerId = $item->id;
        }
        
        // If the type is a module set the type to the sub type
        if($item->typeId == self::$TYPE_ID_MODULE){
        	$type = $this->getTypeByTypeId(self::$TYPE_ID_FORUM);
        }
        else { // Otherwise use the typeId given
        	$type = $this->getTypeByTypeId($item->typeId);
        }
        
 		$typename = get_string($type, 'synch');
 		$hasChildren = is_array($item->children);

 		$dataItemId = $hierarchyController->generateDataItemId($item->id, $item->typeId);
        $baseTabs = $this->createTabString($indent);
 		$output = MDL_NEW_LINE.$baseTabs.'<li id="courseTree-'.$dataItemId.'-listItem" class="'.$type.'" title="'.$type.'">'.MDL_NEW_LINE
					.$baseTabs . MDL_TAB. $this->writeNavigationListItemExpandIcon($item, $hierarchyController, $remoteServerId).MDL_NEW_LINE
					.$baseTabs .MDL_TAB. '<img src="'.$CFG->synch->wwwroot.'/view/images/icon_'.$type.'.gif" alt="'.$typename.'"/>'. MDL_NEW_LINE
					.$baseTabs .MDL_TAB. $item->name.MDL_NEW_LINE;
                    
        if($item->typeId == self::$TYPE_ID_COURSE){
            $output  .=$baseTabs .MDL_TAB. '<a href="session_start.php?id='.$dataItemId;
            if(synch_isset($item->sourceServerId)){
                $output.='&amp;serverId='.$item->sourceServerId;
            }
            $output.='">Synchronise</a>'.MDL_NEW_LINE;
        }
        
        if($item->typeId == self::$TYPE_ID_HUB){
            $output  .=$baseTabs .MDL_TAB. '<a href="server_edit.php?hubId='.$dataItemId.'">Edit</a>'.MDL_NEW_LINE;
        }
	
        if(self::$TYPE_ID_HUB != $item->typeId && 
            (in_array($SynchServerController->getServerId(), $item->serverIds) || in_array($SynchServerController->getRemoteServerId(), $item->serverIds))){	
            $source = '';
            if(in_array($SynchServerController->getServerId(), $item->serverIds) && in_array($SynchServerController->getRemoteServerId(), $item->serverIds)){
            	$source= 'Both';
            }
            else if(in_array($SynchServerController->getServerId(), $item->serverIds)){
                $source= 'Local';
            }	
            else if(in_array($SynchServerController->getRemoteServerId(), $item->serverIds)){
                $source= 'Remote';
            }
            
            $output  .=$baseTabs .MDL_TAB. '('.$source.')'.MDL_NEW_LINE;
        }		
					
		echo $output;
		
		// Write any children
		if(is_array($item->children)){
            echo $baseTabs .MDL_TAB. '<ul id="courseTree-'.$dataItemId.'-list" >'.MDL_NEW_LINE;
            
			$children = $item->children;
			foreach($children as $child){
	 			$this->writeNavigationListItem($child, $hierarchyController, $indent+2, $remoteServerId);
	 		}
 		
			echo $baseTabs .MDL_TAB. '</ul>'.MDL_NEW_LINE;
		}

		echo $baseTabs . '</li>'.MDL_NEW_LINE;

 	}
    
    /*
     * @method getImageForContentItemType Return a string containing an image
     * relating to the content item type passed in
     * @uses $CFG
     * @param string $type Value representing a content type
     * @return string
     */
    public function getImageForContentItemType($type){
    	GLOBAL $CFG;
        $typename = get_string($type, 'synch');
        return '<img src="'.$CFG->synch->wwwroot.'/view/images/icon_'.$type.'.gif" alt="'.$typename.'"/>';
    }
 
     /*
     * Create a string containing tabs to ensure content written to the browser
     * is indented correctly. This makes the code neat and easier to debug.
     * @param int $size number of tabs to add to the output string
     * @return string
     */
    public function createTabString($size=1){
        
        $count = 0;
        $string = '';
        while($size>$count){
            $string.=MDL_TAB;
            $count++;
        }
        
        return $string;
    }
    	
    /*
     * Write the javascript tree data object using the content hierarchy data.
     */
    public function convertArrayToJavascriptArrayString($array){
        $string = '';
        $size = count($array);
        $count = 0;
        foreach($array as $value){
            $count++;
            $string .= '"'.$value.'"';
            if($count<$size){
                $string.=',';
            }
        }
        
        return $string;
    }
    
    /*
     * Write an item in the synch navigation list from the item passed in.
     */
    public function getTypeByTypeId($typeId){
        
        switch($typeId){
            case synch_view_controller::$TYPE_ID_HUB :
                return synch_view_controller::$TYPE_HUB;
                break;
            case synch_view_controller::$TYPE_ID_CATEGORY:
                return synch_view_controller::$TYPE_CATEGORY;
            case synch_view_controller::$TYPE_ID_COURSE:
                return synch_view_controller::$TYPE_COURSE;
            case synch_view_controller::$TYPE_ID_SECTION:
                return synch_view_controller::$TYPE_SECTION;
            case synch_view_controller::$TYPE_ID_MODULE:
                return synch_view_controller::$TYPE_MODULE;
            case synch_view_controller::$TYPE_ID_FORUM:
                return synch_view_controller::$TYPE_FORUM;
        };
    }
    
 	/*
 	 * Save the passed object in the data directory in the synch folder
 	 */
 	public function saveToFile($object, $filename){
 		
 		if(!isset($object)){
 			return false;
 		}
 		GLOBAL $Out;
 		$path = $this->getDataRoot().'/'.$filename;
 		//$Out->append('$path = '.$path);
 		
 		FileSystem::putFileContents($path, $object, true, true);
 		
 	}
 	
 	/*
 	 * Serialise the passed object and save in the data directory in the
 	 * synch folder 
 	 */
 	public function serialiseToFile($object, $filename){
 		
 		if(!isset($object)){
 			return false;
 		}
 		
 		$this->saveToFile(serialize($object), $filename);

 	}
 	
 	/*
 	 * Return file contents from  the data directory in the synch folder. 
 	 */
 	public function retrieveFromFile($filename){

 		
 		$content = FileSystem::getFileContents($this->getDataRoot().'/'.$filename);
 		if(!isset($content)){
 			return null;	
 		}
 		
 		return $content;
 	}
 	
 	/*
 	 * Get a file from  the data directory in the synch folder. Return
 	 * the unserialised results
 	 */
 	public function unSerialiseFromFile($filename){

		$contents = $this->retrieveFromFile($filename);

 		if(!isset($content)){
 			return null;	
 		}
 		
 		$object = unserialize($content);

 		return $object;
 	}
 	
 	/*
 	 * Get the serialised nav file from the ContentHierarchy class and save the
 	 * data to the data directory
 	 */
 	public function backupHierarchy(){
 		GLOBAL $SynchContentHierarchy;
 		
 		$backup = $SynchContentHierarchy->serializeHierarchyData();
 		$this->saveToFile($backup, $this->getSerialisedNavFileName());
		
 	}
 	
 	/*
 	 * Get the serialised nav file from  the data directory in the synch folder
 	 * and load the data back into the Content Hierarchy
 	 */
 	public function restoreHierarchy(){
 		// Load cached data
	    $cachedHierarchy = $this->retrieveFromFile($this->getSerialisedNavFileName());
	       
	    GLOBAL $SynchContentHierarchy;
	    $SynchContentHierarchy->restoreHierarchyData($cachedHierarchy);

 	}
 } 
?>
