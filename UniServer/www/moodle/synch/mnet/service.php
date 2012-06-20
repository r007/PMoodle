<?php
/*
 *
 * @copyright &copy; 2006 The Open University
 * @author d.t.le@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * This file is used in combination with synch.php to provide simple methods for
 * the web service used by the synchronisation process. This extra layer is
 * provided to make it easy to test and develop the functions 
 */
 require_once dirname(__FILE__)."/../admin/synch-setup.php";
 GLOBAL $CFG;

    GLOBAL $Out;
    if(!defined('BACKUP_SILENTLY')){
        define('BACKUP_SILENTLY', true);
    }
    
    if(!defined('RESTORE_SILENTLY_NOFLUSH')){
        define('RESTORE_SILENTLY_NOFLUSH', true);
    }
    
    /* @method getHierarchyData designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @param string $id courseid to backup
     * @return mixed
     */
    function Synch_retrieveBackupById($id, $hostUrl, $sessionId){
        
        GLOBAL $SynchManager;
        return $SynchManager->retrieveBackupById($id, $hostUrl, $sessionId);
    }
    
    /* @method getHierarchyData designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @param string $id courseid to backup
     * @return mixed
     */
    function Synch_restoreMergedBackupFromRemoteHost($id, $hostUrl, $sessionId, $fileName){
        
        GLOBAL $SynchManager, $USER;
        $USER = guest_user();

        return $SynchManager->restoreMergedBackupFromRemoteHost($id, $hostUrl, $sessionId, $fileName);
    }
    
    /* @method Synch_getServerDetails designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @return object
     */
    function Synch_getServerDetails(){
    	GLOBAL $SynchServerController;
        $details = new stdClass;
        $details->id = $SynchServerController->getServerId(); 
        
        return $details;
    }
    
    /* @method Synch_getHierarchyChildrenByIdAndType get the children for a specific hierarchy item.
     * @param int id parent id
     * @param int type 
     * @return array SynchContentItem
     */
    function Synch_getHierarchyChildrenByIdAndType($id, $type){
        GLOBAL $SynchViewController;
        $children = $SynchViewController->getChildrenByIdAndType($id, $type); 
        
        return $children;
    }
    
    /* @method getHierarchyData designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @param string $id courseid to backup
     * @return mixed
     */
    function Synch_getDetailsByDataItemId($dataItemId){

        GLOBAL $SynchManager;
        return $SynchManager->getDetailsByDataItemId($dataItemId);
    }
?>
