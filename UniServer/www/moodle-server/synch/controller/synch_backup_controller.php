<?php
 /*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * The backup and restore process is a core part of the synchronisation process.
 * This controller encapsulates the backup and restore code to provide simple
 * reliable methods to coordinate the process effectively and efficiently.
 */
 
 class synch_Backup_controller{
 	
    /*
     * @uses $CFG
     */
    public static function createBackupFilePathFromPreferences($preferences){
    	if(!isset($preferences)){
            return null;
        }
        
        GLOBAL $Out;
        $name = synch_Backup_controller::getItemFromPreferences($preferences, 'backup_name');
        $courseId = synch_Backup_controller::getItemFromPreferences($preferences, 'backup_course');
        
        return self::createBackupFilePath($courseId).'/'.$name;
        //GLOBAL $CFG;
        //return $CFG->dataroot."/".$courseId.'/backupdata/'.$name;
    }
    
    /*
     * @uses $CFG
     */
    public static function createBackupFilePath($courseId){
        if(!isset($courseId)){
            return null;
        }
       
        GLOBAL $CFG;
        return $CFG->dataroot."/".$courseId.'/backupdata';
    }
    
    /* @method moveBackupToQueue designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @param string $id courseid to backup
     * @return mixed
     */
    public static function getItemFromPreferences($preferences, $name){
        if(!isset($preferences) || !isset($preferences->$name)){
            return null;
        }
        
        return $preferences->$name;
    }
    
    /* @method getBackupNameWithUniqueCode designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @param string $id courseid to backup
     * @return mixed
     */
    public static function getBackupNameWithUniqueCode($preferences){
        if(!isset($preferences)){
            return null;
        }
        
        GLOBAL $Out;
        $name = synch_Backup_controller::getItemFromPreferences($preferences, 'backup_name');
        $uniqueCode = synch_Backup_controller::getItemFromPreferences($preferences, 'backup_unique_code');

        // append the unique code to the backup name
        $strings = explode('.', $name);

        if(!is_array($strings)){
            // no file extension
            return $name.'-'.$uniqueCode;
        }

        $strings[count($strings)-2].='-'.$uniqueCode;
        return implode('.', $strings);

    }
    
    /* @method getBackupNameWithUniqueCode designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @param string $id courseid to backup
     * @return mixed
     */
    public static function getUniqueCodeFromFileName($fileName){
    	if(!isset($fileName)){
            return null;
        }
        
        
        return FileSystem::getFromFileName($fileName, 2);
    }
    
    /* @method restoreCourse designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @param string $id courseid to backup
     * @return mixed
     */
    public static function restoreCourse($preferences, $session){
    	
        global $Out;
        // the initial set of preferences should be enough to get the restore started. 
        // once in progress the restore will obtain the preferences from the backup file
        // itself
         
        if(!isset($preferences)){
            return null;
        }
        
        // Assumes the backup file is in the course data directory and the 
        // preferences are in the backup file itself.  
        GLOBAL $CFG;
        
        // for large files uncomment following code
        //@ini_set("max_execution_time","3000");
        //raise_memory_limit("192M");
        
        $file = self::createBackupFilePath($preferences->course_id);
        $file .= '/'.$preferences->backup_name; //path to file
        
        //Check the file exists 
        if (!is_file($file)) {
            return false;      
        }
        
        //Check the file name ends with .zip
        if (!substr($file,-4) == ".zip") {
            return false;  
        }
        //Now calculate the unique_code for this restore
        $backup_unique_code = $preferences->backup_unique_code;
        
        //Now check and create the backup dir (if it doesn't exist)
        $status = check_and_create_backup_dir($backup_unique_code);
        //Empty dir
        if ($status) {
            $status = clear_backup_dir($backup_unique_code);
        }
        
        //Now delete old data and directories under dataroot/temp/backup
        if ($status) {   
                $status = backup_delete_old_data();
        }
        
        $tempBackupPath = synch_backup_controller::createTempBackupPath($backup_unique_code);
        
        //Now copy the zip file to dataroot/temp/backup/backup_unique_code
        if ($status) {

            if (! $status = backup_copy_file($file,$tempBackupPath."/".basename($file))) {
                // There has been a problem. Invalid name or bad perms
                return false;  
            }
        }
        
        //Now unzip the file
        if ($status) {
            if (! $status = restore_unzip ($tempBackupPath."/".basename($file))) {
                // error: Invalid zip file
                return false;
            }
        }

        
        //Check for Blackboard backups and convert
        if ($status){
            require_once("$CFG->dirroot/backup/bb/restore_bb.php");
            $status = blackboard_convert($tempBackupPath);
        }
        
        // backup file has now been unpacked. Retrieve the serialized preferences
        $preferencesPath = $tempBackupPath.'/'.self::getPreferencesFileName();
        $preferences = FileSystem::unSerializeFromFile($preferencesPath);
        
        // Now we have the preferences from the backup we need to tailor it to our current needs
        // should we be updating an existing item or creating one.
        $dataItemId = SynchContentHierarchy::generateDataItemId($preferences->course_id, synch_view_controller::$TYPE_ID_COURSE);
        global $SynchManager, $SynchServerController;
        $itemExists = $SynchManager->getSessionItemExistsByServerId($SynchServerController->getServerId(), $session);
        if(isset($itemExists) && is_array($itemExists) && in_array($dataItemId, $itemExists)){
        	$preferences->restoreto = 1;
            $preferences->deleting = 1;
        }
        else {
        	$preferences->restoreto = 2;
        }
        
        //Now check for the moodle.xml file
        if ($status) {
            $xml_file  = $tempBackupPath."/moodle.xml";
            if (! $status = restore_check_moodle_file ($xml_file)) {
                if (!is_file($xml_file)) {
                    //Error checking backup file. moodle.xml not found at root level of zip file
                    return false;
                } else {
                    //Error checking backup file. moodle.xml is incorrect or corrupted.
                    return false;
                }
            }
        }
        
    
    
        //unpack backup file 
        
        //read contents
        //Reading info from file
        $info = restore_read_xml_info ($xml_file);
        
        //Reading course_header from file
        $courseHeader = restore_read_xml_course_header ($xml_file);
        
        //Save course header and info into php session
        if ($status) {
            //$SESSION->info = $info;
            //$SESSION->course_header = $course_header;
        }
        
        GLOBAL $restore;
        $restore = $preferences;
        $message = null;
        $restoreSuccess = restore_execute($preferences,$info,$courseHeader,$message);
        
        return $restoreSuccess;
    }
    
    /*
     * @method createTempBackupPath
     * @return string
     */
    public static function createTempBackupPath($backup_unique_code){
    	
        if(!isset($backup_unique_code)){
        	return null;
        }
        
        GLOBAL $CFG;
        return $CFG->dataroot."/temp/backup/".$backup_unique_code;
    }
    
    /*
     * @method createTempBackupPath
     * @return string
     */
    public static function getPreferencesFileName(){
    	return 'preferences.txt';
    }
 }
?>
