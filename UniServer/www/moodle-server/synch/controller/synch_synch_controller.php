<?php

/**
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * This class is the main controller for the synchronisation process. It
 * contains methods to manage the synchronisation process and coordinate the
 * other controlling classes available. It also contains methos to record and
 * mainpulate data used for synchronisation
 */
global $CFG;

/*
 * Begin import statements
 */
 
// Import necessary files
require_once($CFG->dirroot.'/synch/controller/synch_configuration_controller.php');
require_once($CFG->dirroot.'/synch/modal/SynchTypeConfiguration.php');

// include extra set of library files
require_once $CFG->dirroot.'/synch/lib/import_all.php';

// Includes necessary files for Backup
require_once $CFG->dirroot.'/backup/backuplib.php';
require_once $CFG->dirroot.'/backup/lib.php';
require_once $CFG->libdir.'/blocklib.php';
require_once $CFG->libdir.'/adminlib.php';

// Ensure backup messages are suppressed 
if(!defined('BACKUP_SILENTLY')){
    define('BACKUP_SILENTLY', true);
}

// Include necessary files for Restore
require_once $CFG->libdir.'/xmlize.php';
require_once $CFG->dirroot.'/course/lib.php';
require_once $CFG->dirroot.'/backup/restorelib.php';
require_once $CFG->dirroot.'/backup/bb/restore_bb.php';
require_once $CFG->libdir.'/wiki_to_markdown.php';

// Ensure restore messages are suppressed
if(!defined('RESTORE_SILENTLY')){
    define('RESTORE_SILENTLY', true);
}

if(!defined('RESTORE_SILENTLY_NOFLUSH')){
    define('RESTORE_SILENTLY_NOFLUSH', true);
}

/**
 * This class handles all aspects of synch
 */
class synch_Synch_controller {

    /*
     * Used	 to	 indicate the type of action either being recorded or
     * synchronised. Possible options are create, read, update or delete
     */
	public static $ACTION_CREATE = 1;
	public static $ACTION_READ = 2;
	public static $ACTION_UPDATE = 3;
	public static $ACTION_DELETE = 4;

    /*
     * Proof of concept variables
     * 
     * These variables were implemented during the proof of concept phase. They
     * remain because the POC code is yet to be integrated with the main
     * synchronisation process. removing them is likely to make the POC code
     * more difficult to understand and cease to function.
     * 
     * @var array $tables method of abstracting the db table name to a common
     * reference. This was implented simply because the table name may change
     * so we didn't want to hard code it. This way the physical name can change
     * regularly but the reference needn't and the change need only be
     * implemented in one place. 
     * 
     */
	var $tables = array();
    
    /*
     * For the proof of concept connections to both the remote and local
     * databases where maintained in an array for ease of use
     * @var array $databases 
     */
	var $databases = array();
	
    /*
     * A reference  to the current database from the $databases array is stored
     * here for easy access
     * @var stdClass $currentDatabase 
     */
	var $currentDatabase = null;
    
    /*
     * End of prooof of concept variables
     */
	
    /*
     * The backup and restore process was originally managed in this class. Thus
     * the preferences were stored and accessed here. This is likely to be
     * deprecated in time
     * @var mixed $backupPreferences 
     */
    protected $backupPreferences = null;
	
	function __construct(){

        /*
         * Proof of concept code
         * 
         * The construct sequence was developed for the proof of concept where
         * the other controllers had not be developed and there was a need for
         * direct database to database data transfer
         */
		$this->tables["synch"] = "synch";
        
        /*
         * A general cache object which has been superceded by the session
         * object and controller
         * @var $this->cache
         */
		$this->cache = new Object();
        /*
         * there was a requirement to translate the remote content to the local
         * context. To reduce db traffic the remote context was cached
         * 
         * @var mixed $this->cache->remoteContext
         */
		$this->cache->remoteContext = null;
	}
	
    /*
     * You have to wait for the object to be built before you can actually
     * do anything so now we can run any intialisation processes
     * @method initialise
     * @return void
     */
	function initialise(){
        $this->createCacheOfRemoteContext();
	}
    
    /*
     * @method getBackupPreferences
     * @return object backup preferences
     */
    public function getBackupPreferences(){
    	return $this->backupPreferences;
    }
    
    /*
     * @method setBackupPreferences
     * @param object $new backup preferences
     * @return void
     */
    public function setBackupPreferences($new){
        $this->backupPreferences = $new;
    }
    
    /*
     * @method createLocalBackup
     * @param string $dataItemId the id of the item to include in a backup
     * @param mixed $session the related session object for reference
     * @return bool
     */
    public function createLocalBackup($dataItemId, $session){
        
        global $SynchViewController, $SynchSessionController;
        global $SynchContentHierarchy;
        $id = SynchContentHierarchy::getIdFromDataItemId($dataItemId);
        $type = SynchContentHierarchy::getTypeFromDataItemId($dataItemId);
        $created = $this->createBackupByIdAndType($session, $id, $type);
        // Does the item exist locally
            // yes: make a backup and include the synch logs

        if(!$created){
            return null;
        }
    
        // if a backup was made move it into the session container
        $moved = $this->moveLocalBackupToSessionContainer($this->getBackupPreferences(), $session);
         if(!$moved){
            return null;
        }
        return $this->createSessionBackupFileName($this->getBackupPreferences());;
    }
    
    /*
     * Use the session controller to Set the current session by its id and
     * return the session object
     * @method moveLocalBackupToSessionContainer
     * @param object $preferences 
     * @return bool
     */
    public function moveLocalBackupToSessionContainer($preferences, $session){
    	
        if(!isset($preferences) || !isset($session)){
        	return false;
        }
        
        // Move the backups from the course directory into synch/sessions/sessionid/backups with the serverid appended the file name
        // Generate a new file name from the original backup file and the local server id
        $newFileName = $this->createSessionBackupFileName($preferences);
        // Generate the path to the session backup folder
        $sessionBackupPath = $this->createSessionBackupPath($session);
        $courseBackupPath = synch_Backup_controller::createBackupFilePathFromPreferences($preferences);
        
        // Create the folder structure if necessary
        
        FileSystem::createFoldersFromPath($sessionBackupPath);
        return FileSystem::moveFile($courseBackupPath, $sessionBackupPath.'/'.$newFileName);
    }
    
    /*
     * Use the session controller to Set the current session by its id and
     * return the session object
     * @method createSessionBackupFileName
     * @param object $preferences 
     * @return bool
     */
    public function createSessionBackupFileName($preferences){
         
        if(!isset($preferences)){
            return null;
        }
        
        $name = synch_Backup_controller::getItemFromPreferences($preferences, 'backup_name');
        $uniqueCode = synch_Backup_controller::getItemFromPreferences($preferences, 'backup_unique_code');
        $serverId = synch_Backup_controller::getItemFromPreferences($preferences, 'server_id');

        $name = FileSystem::appendToFileName($name, '.'.$uniqueCode);
        $name = FileSystem::appendToFileName($name, '.'.$serverId);
        
        return $name;     
    }
    
    /*
     * Return a path to a session folder given a session object
     * @method createSessionPath
     * @param synch_Session $session 
     * @return string
     */
    public function createSessionPath($session){
    	if(!isset($session)){
            return null;
        }
        
        global $CFG;
        $sessionPath = $CFG->synch->dataroot.'/sessions/'.$session->id;
        return $sessionPath;
    }
    
    /*
     * Return a path to a sessions backups folder given a session object
     * @method createSessionBackupPath
     * @param synch_Session $session 
     * @return string
     */
    public function createSessionBackupPath($session){
    	global $CFG;
        return $this->createSessionPath($session).$CFG->synch->path_backups;
        
    }
    
     /*
     * Use the session controller to Set the current session by its id and
     * return the session object
     * @method restoreCurrentSessionById
     * @param string $dataItemId
     * return bool
     */
    public function restoreCurrentSessionById($id){
    	global $SynchSessionController;
        $SynchSessionController->setCurrentSessionById($id);
        return $SynchSessionController->getCurrentSession();
    }
    
    /*
     * @method hasLocalInstance
     * @param string $dataItemId
     * return bool
     */
    public function hasLocalInstance($id, $type){
    	
    }
    
    /*
     * Use the chain of methods already provided to generate a Moodle backup
     * silently
     * @method createBackupForCourse
     * @param string $dataItemId
     * return bool
     */
    public function createBackupForCourse($session, $id){
        
        global $Out;
        // Does the item exist locally
        $record = get_record("course", "id", "$id", null, null, 
                                null, null, "id");
        $Out->print_r($record, '$record = ', 0, true);
        
        global $CFG;
        if(empty($record)){
            return false;
        }
        
        $preferences = $this->getOrCreateSessionBackupPreferences($session);
        // yes: make a backup and include the synch logs
        $this->appendPreferencesForCoreBackup($preferences);
        $this->appendPreferencesForCourseBackup(&$preferences, $id);
        $preferences->backup_generate_incrementals = true; // Ensure incrementals are created
        $this->setBackupPreferences($preferences);
        $CFG->backup_preferences = $preferences;
        $message = '';
        $Out->print_r($preferences, '$preferences = ', 0, true);
        return backup_execute($preferences, $message);
        
    }
    
    /*
     * Create a backup object and append it to the session object passed in.
     * @method createSessionBackupObject
     * @param synch_Session $session
     * @return object containing new backup object. 
     */
    public function createSessionBackupObject($session){
        if(empty($session)){
            return null;
        }
        
        $session->backup = new stdClass;
        
        return $session->backup;
    }
    
    /*
     * Create a preferences object for the backup process and append it to the
     * session object passed in.
     * @method createSessionBackupPreferences
     * @param synch_Session $session
     * @return object containing preferences that have been created. 
     */
    public function createSessionBackupPreferences($session){
    	if(empty($session)){
    		return null;
    	}
        
        if(empty($session->backup)){
        	$this->createSessionBackupObject($session);
        }
        $session->backup->preferences = new stdClass;
        return $session->backup->preferences;
    }
    
    /*
     * Return a preferences object having first initialised it if it doesn't
     * exist
     * @method getOrCreateSessionBackupPreferences
     * @param synch_Session $session
     * @return preferences object. 
     */
    public function getOrCreateSessionBackupPreferences($session){
    	if(empty($session)){
            return null;
        }
        
        if(empty($session->backup->preferences)){
            $this->createSessionBackupPreferences($session);
        }
        
        return $session->backup->preferences;
    }
    
    
    /*
     * Append flags to back up and restore the core Moodle data to the
     * preference object passed in.
     * @method appendPreferencesForCoreBackup
     * @param object $preferences
     * @return object. 
     */
    public function appendPreferencesForCoreBackup($preferences){
        /*
         * Create a preferences object that will record the core data in the
         * backup.
         */
        if(empty($preferences)){
            return null;
        }
        
        $preferences->backup_core_data = 1;
        $preferences->restore_core_data = 1;
        
        return $preferences;
    }
    
    
    /*
     * Append flags to back up and restore the relevant forums
     * @method appendPreferencesForForumsBackup 
     * @param object $preferences
     * @param string $courseId
     * @return void 
     */
    public function appendPreferencesForForumsBackup($preferences, $courseId){
    	// Set the preferences to backup all the forums in the course. 
        
        $preferences->exists_forum = 1;
        
        $preferences->exists_one_forum = 1;
        
        $preferences->forum_instances = array();
        
        $preferences->mods['forum'] = new Object();
        $preferences->mods['forum']->name = 'forum';
        $preferences->mods['forum']->backup = 1;
        $preferences->mods['forum']->restore = 1;
        $preferences->mods['forum']->granular = 1;
        $preferences->mods['forum']->userinfo = 1;
        $preferences->mods['forum']->instances = array();
        
        $preferences->backup_forum = 1;
        $preferences->backup_user_info_forum = 1;
        $preferences->backup_forum_instances = 1;
        
        
        // Get the forums for the course. 
        $records = get_records('forum', 'course', $courseId);
        
        $instance = null;
        $count= 0;
        foreach($records as $record){
            // Forums instances
            $instance = new Object();
            $instance->id = $record->id;
            $instance->course = $record->course; 
            $instance->type = $record->type;
            $instance->name = $record->name;
            
            $preferences->forum_instances[] = $instance;
            
            // Create instances
            $instance = new Object();
            $instance->name = $record->name;
            $instance->backup = 1;
            $instance->restore = 1;
            $instance->userinfo = 1;
            $preferences->mods['forum']->instances[$record->id] = $instance; // reflects module id
            
            $count = count($preferences->forum_instances);
            $property = 'backup_forum_instance_'.$count;
            $preferences->$property = 1;
            $property = 'backup_user_info_forum_instance_'.$count;
            $preferences->$property = 1;
            
        }
        
    }
    
    /* Append quiz preferences that
     * the backup and restore processes use to backup and restore courses and
     * their contents.
     * @method appendPreferencesForQuizzesBackup 
     * @param object $preferences
     * @param string $courseId
     * @return void 
     */
    public function appendPreferencesForQuizzesBackup($preferences, $courseId){
        // Set the preferences to backup all the forums in the course. 
        
        $preferences->exists_quiz = 1;
        
        $preferences->exists_one_quiz = 1;
        $preferences->quiz_instances = array();
        
        $preferences->mods['quiz'] = new Object();
        $preferences->mods['quiz']->name = 'quiz';
        $preferences->mods['quiz']->backup = 1;
        $preferences->mods['quiz']->restore = 1;
        $preferences->mods['quiz']->granular = 1;
        $preferences->mods['quiz']->userinfo = 1;
        $preferences->mods['quiz']->instances = array();
        
        $preferences->backup_quiz = 1;
        $preferences->backup_user_info_quiz = 1;
        $preferences->backup_quiz_instances = 1;
        
        
        // Get the quizs for the course. 
        $records = get_records('quiz', 'course', $courseId);
        // backups prefs 31
        // quiz fields: 28
        
        $instance = null;
        $count= 0;
        foreach($records as $record){
            // Quizs instances
            $instance = new Object();
            $instance->id = $record->id;
            $instance->course = $record->course;
            $instance->type = $record->type;
            $instance->name = $record->name;
        
            $preferences->quiz_instances[] = $instance;
        
            // Create instances
            $instance = new Object();
            $instance->name = $record->name;
            $instance->backup = 1;
            $instance->restore = 1;
            $instance->userinfo = 1;
            $preferences->mods['quiz']->instances[$record->id] = $instance; // reflects module id
        
            $count = count($preferences->quiz_instances);
            $property = 'backup_quiz_instance_'.$count;
            $preferences->$property = 1;
            $property = 'backup_user_info_quiz_instance_'.$count;
            $preferences->$property = 1;
        
        }
        
    }
    
    /*
     * For each module, get the relevant preferences added to the
     * preference object to be used in the backup and restore process.
     * @method appendPreferencesForModulesBackup 
     * @param object $preferences
     * @param string $courseId
     * @return void  
     */
    public function appendPreferencesForModulesBackup($preferences, $courseId){
        
        $preferences->mods = array();
        $this->appendPreferencesForForumsBackup($preferences, $courseId);
        $this->appendPreferencesForQuizzesBackup($preferences, $courseId);
    }
    
    /*
     * The backup and restore process uses preferences to control what a backup
     * contains and a restore restores. The preferences are generated in this
     * method. 
     * @method appendPreferencesForCourseBackup 
     * @param object $preferences
     * @param string $courseId
     * @return object containing preferences that have been created. 
     */
    public function appendPreferencesForCourseBackup($preferences, $courseId){
        
        if(empty($preferences)){
            return null;
        }
        
        /*
         * We were using this manual process to enable backup and restore
         * for each module
        $preferences->exists_assignment = 1;
        $preferences->exists_chat = 1;
        $preferences->exists_choice = 1;
        $preferences->exists_data = 1;
        $preferences->exists_exercise = 1;
        
        $this->appendPreferencesForModulesBackup($preferences, $courseId);
     
        $preferences->exists_glossary = 1;
        $preferences->exists_hotpot = 1;
        $preferences->exists_journal = 1;
        $preferences->exists_label = 1;
        $preferences->exists_lesson = 1;
        $preferences->exists_quiz = 1;
        $preferences->exists_resource = 1;
        $preferences->exists_scorm = 1;
        $preferences->exists_survey = 1;
        $preferences->exists_wiki = 1;
        $preferences->exists_workshop = 1;
        $preferences->backup_course = $courseId; //1010000002;
         
         
        $preferences->backup_metacourse = 1;
        $preferences->backup_users = 1;
        $preferences->backup_logs = 1;
        $preferences->backup_user_files = 1;
        $preferences->backup_course_files = 1;
        $preferences->backup_messages = 1;
        */
        
        global $Out;
        $prefs['backup_metacourse'] = 1;
        $prefs['backup_users'] = 1;
        $prefs['backup_logs'] = 1;
        $prefs['backup_user_files'] = 1;
        $prefs['backup_course_files'] = 1;
        $prefs['backup_messages'] = 1;
        
        
        $course = get_record('course', 'id', $courseId);
        /*
         * For now we're using the inbuilt method to automatically produce a
         * preferences object which includes all course content. 
         */
        $Out->print_r($preferences, '$preferences = ', 0, true);
        $preferencesNew = backup_generate_preferences_artificially($course, $prefs);
        $Out->print_r($preferencesNew, '$preferencesNew = ', 0, true);
        //$preferences = array_merge($preferences, $preferencesNew);
        foreach($preferences as $key => $value){
        	$preferencesNew->$key = $value;
        }
        $preferences = $preferencesNew;

        if(!empty($preferences->mods)){
            // Set each module restore flag
            foreach($preferences->mods as $module){
            	$module->restore=1;
                foreach($module->instances as $instance){
                	$instance->restore=1;
                }
            }
        }
        /*
        $date = new NumDate();
        $preferences->backup_name = 'backup-teco1-'.$date->getDate().'-'.$date->getHour().$date->getMinute().'.zip';
        $preferences->backup_unique_code = time(); //'1185376218';
        $preferences->moodle_version = '2007071900';
        $preferences->moodle_release = '1.9 dev';
        $preferences->backup_version = '2007022100';
        $preferences->backup_release = '1.9dev';
        */
        //$preferences->restoreto = 1; // Overwrite the course
        $preferences->restoreto = 2; // Restore to a new course
        $preferences->restore_restorecatto = 1;
        $preferences->restore_maintain_ids = true;
        
        $preferences->course_id = $courseId; //1010000002;
        $preferences->course_startdateoffset = 0;
        $preferences->metacourse = 1;
        $preferences->users = 1;
        $preferences->quiz = 1;
        $preferences->user_files = 1;
        $preferences->course_files = 1;
        $preferences->messages = 1;
        $preferences->deleting = 0; //delete contents of a course before restoring into it.
        
        global $SynchServerController;
        // Append the server id
        $serverId = $SynchServerController->getServerId();
        $preferences->server_id = $serverId; 
        
        global $SynchSessionController;
        if(isset($SynchSessionController)){
            // Append the session id
            $sessionId = $SynchSessionController->getCurrentSessionId(); 
            $preferences->session_id = $sessionId;
        } 
        
        return $preferences;
        
    }
    
    
    public function getServerDetailsFromDB($ids){
    	
        global $CFG;
        if(is_array($ids)){
        	$extra = ' AND s.serverId IN(\''.implode($ids, ',').'\')';
        }
        
        $sql = '  SELECT 
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
                                    s.mnetHostId = h.id'.$extra;
        //$Out->print_r($sql, '$sql = ');
        // Get the server details 
        $hosts = get_records_sql($sql);
         
         return $hosts;
    }
    
    public function getServerDetailsFromDBByServerId($id){
    	if(empty($id)){
    		return null;
    	}
        $servers = $this->getServerDetailsFromDB(array($id));
        if(!is_array($servers)){
        	return null;
        }
        
        return array_shift($servers);
    }
    
    /* @method getHierarchyData designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @param string $id courseid to backup
     * @return mixed
     */
    public function getBackupById($id, $session){
        
        $localBackup = $this->createLocalBackup($id, $session);
        return $id;
    }
    
    /* @method createBackupByIdAndMoveToQueue Create the backup according to the given id and move it to the synch queue
     * @param string $id courseid to backup
     * @return string name of backup file
     */
    public function createBackupByIdAndMoveToQueue($id, $session){
    	$this->getBackupById($id, $session);
        
        // Move the backup file to the synch queue.
        $this->moveBackupToQueue($this->getBackupPreferences());
        
        return $this->createSessionBackupFileName($this->getBackupPreferences());
    }
    
    /* @method retrieveBackupById Create the backup according to the given id and move it to the synch queue
     * @param string $id courseid to backup
     * @return string name of backup file
     */
    public function retrieveBackupById($id, $hostUrl, $sessionId){
    	
        // Restore session with session id
        global $SynchSessionController;
        $session = $SynchSessionController->createSessionAsCurrent(true, array('id'=>$sessionId));
    	$this->createBackupByIdAndMoveToQueue($id, $session);
        $preferences = $this->getBackupPreferences();
        $path = $this->createSynchItemPathFromPreferences($preferences, 1);
        $fileName = $this->createSessionBackupFileName($preferences);
        $path.='/'.$fileName;
        $url = $hostUrl.'/synch/queue.php';
        $this->uploadFileToRemoteHost($path, $url);
        
        return $fileName;
    }
    
    /* @method uploadFileToRemoteHost Upload a file to a given host
     * @param string $id courseid to backup
     * @return string name of backup file
     */
    public function uploadFileToRemoteHost($path, $url, $deleteFile=true){
    	global $SynchSessionController;
        $sessionId = $SynchSessionController->getCurrentSessionId();
        $fileUploader = new CurlFileUploader($path, $url,'file1', array('sessionId'=>$sessionId));
        $fileUploader->UploadFile();
        
        if($deleteFile){
        	
            FileSystem::deleteFile($path);
        }
        return true;
    }
    
    /* @method moveBackupToQueue designed for testing. Gets the data from
     * the ContentHierarchy of another moodle instance
     * @param string $id courseid to backup
     * @return mixed
     */
    public function moveBackupToQueue($preferences){
 
        if(!isset($preferences)){
        	return false;
        }
        
        global $CFG, $SynchSessionController;
        
        // Generate the path to the session backup folder
        $session = $SynchSessionController->getCurrentSession();
        $fileName = $this->createSessionBackupFileName($preferences);
        $pathFrom = $this->createSessionBackupPath($session);
        $pathFrom .= '/'.$fileName;
        $pathTo = $this->createSynchItemPathFromPreferences($preferences, 1);
        $pathTo .= '/'.$fileName;
        
        //Check the file exists and check the file name ends with .zip
        if (!is_file($pathFrom) && !substr($pathFrom,-4) == ".zip") {
            return false;
        }
        
        
        FileSystem::createFoldersFromPath(FileSystem::path_pop($pathTo));//$CFG->synch->path_queue_out
        FileSystem::moveFile($pathFrom, $pathTo);
    }
    
    /* 
     * Generate a file system path to the backup folder from the preferences 
     * @method createBackupPathFromPreferences 
     * @param stdClass $preferences
     * @return string
     */
    public function getDetailsByDataItemId($dataItemId){
    	
        $type = SynchContentHierarchy::getTypeFromDataItemId($dataItemId);
        $id = SynchContentHierarchy::getIdFromDataItemId($dataItemId);
        
        $details = null;
        switch($type){
        	case synch_view_controller::$TYPE_ID_COURSE:
                
                $details = get_record('course', 'id', $id);
                break;
        }
        
        return $details;
    }
    
    /* 
     * Generate a file system path to the backup folder from the preferences 
     * @method createBackupPathFromPreferences 
     * @param stdClass $preferences
     * @return string
     */
    public function createBackupPathFromPreferences($preferences){
    	if(!isset($preferences)){
            return null;
        }
        
        global $CFG;
        //return $preferences->course_id.'/backupdata/'.$preferences->backup_name; //path to file
        $name = synch_Backup_controller::getItemFromPreferences($preferences, 'backup_name');
        $id = synch_Backup_controller::getItemFromPreferences($preferences, 'course_id');
        return $CFG->dataroot."/".$id.'/backupdata/'.$name; //path to file
    }
    
    /* 
     * Generate a file system path to the relevant queue and session from the
     * preferences and type provided
     * @method createSynchItemPathFromPreferences 
     * @uses $CFG
     * @param stdClass $preferences 
     * @param int $type currently 0=in queue, 1= out queue
     * @return string
     */
    public function createSynchItemPathFromPreferences($preferences, $type=0){
        if(!isset($preferences)){
            return null;
        }
        
        global $CFG;
        $name = synch_Backup_controller::getBackupNameWithUniqueCode($preferences);
        $id = synch_Backup_controller::getItemFromPreferences($preferences, 'course_id');
        $pathToQueue = $type? $CFG->synch->path_queue_out:$CFG->synch->path_queue_in;
        
        $sessionId = synch_Backup_controller::getItemFromPreferences($preferences, 'session_id');
        if(isset($sessionId)){
        	$pathToQueue.='/'.$sessionId; //path to file
    }
    
        return $pathToQueue;
    }
    
    /* 
     * Generate a file system path string to the relevant queue and session from
     * the sessionId and type passed in.
     * @method createQueueSessionPath
     * @uses $CFG
     * @param string $sessionId 
     * @param int $type currently 0=in queue, 1= out queue
     * @return string
     */
    public function createQueueSessionPath($sessionId, $type=0){
        if(!isset($sessionId)){
            return null;
        }
        global $CFG;
        $pathToQueue = $type? $CFG->synch->path_queue_out:$CFG->synch->path_queue_in;
        return $pathToQueue.'/'.$sessionId; //path to file
        
    }
    
    /*
     * Create a backup by the id and type passed in
     * @param synch_Session_controller $session
     * @param int id parent id
     * @param int type 
     * return bool
     */
    public function createBackupByIdAndType($session, $id, $type){
        
        $backedup = false;
        switch($type){
            case synch_view_controller::$TYPE_ID_HUB:
                //$children = $this->getBackupForHub();
                break;
                
            case synch_view_controller::$TYPE_ID_CATEGORY:
                //$children = $this->createBackupForCategory($id);
                break;
                
            case synch_view_controller::$TYPE_ID_COURSE:
                $backedup = $this->createBackupForCourse($session, $id);
                break;
            
        }
        
        return $backedup;
        //global $SynchContentHierarchy;
        //$SynchContentHierarchy->loadData($children, $type, $id);
    }
    
    /*
     * Set a flag in the session by server id to indicate whether or
     * note there are changes to merge. 
     * @method setSessionHasChangesByServerId
     * @param bool $hasChanges
     * @param string $serverId
     * @param object $session
     * @return bool
     */
    public function setSessionHasChangesByServerId($hasChanges, $serverId=null, $session){
        if(empty($session->servers->$serverId)){
            return false;
        }
        
        $session->servers->{$serverId}->has_changes = $hasChanges;
        return true;
    }
    
    /*
     * Convenience method to aid the merging
     * process. If has changes is not set merging can be bypassed
     * @method getSessionHasChangesByServerId
     * @param string $serverId
     * @param object $session
     * @return bool
     */
    public function getSessionHasChangesByServerId($serverId=null, $session){
        if(empty($session->servers->$serverId) || !isset($session->servers->{$serverId}->has_changes)){
            return false;
        }
        $server = $session->servers->$serverId;
        return $session->servers->{$serverId}->has_changes;
    }
    
     /*
     * @method setSessionItemExistsByServerId
     * @param array $dataItemIds
     * @param string $serverId
     * @param object $session
     * @return bool
     */
    public function setSessionItemExistsByServerId($dataItemIds, $serverId=null, $session){
        if(empty($session->servers->$serverId)){
    		return false;
    	}
        
        $session->servers->{$serverId}->item_exists = $dataItemIds;
        return true;
    }
    
    /*
     * Convenience method to check a session for the item exists flag for a
     * server by its id
     * @method getSessionItemExistsByServerId
     * @param string $serverId
     * @param object $session
     * @return array
     */
    public function getSessionItemExistsByServerId($serverId=null, $session){
        if(empty($session->servers->$serverId) 
            || empty($session->servers->{$serverId}->item_exists) 
            || !is_array($session->servers->{$serverId}->item_exists)){
            return null;
        }
        
        return $session->servers->{$serverId}->item_exists;
    }
    
    public function getRemoteHierarchyChildrenByIdAndType($serverId, $id, $type){
    	        
        $method = 'synch/mnet/synch.php/getHierarchyChildrenByIdAndType';
        $parameters = array(array($id, 'string'),
                        array($type, 'string')
                        );
        global $Out;   
        $response = $this->callRemoteMethod($method, $parameters);
        //$Out->print_r($response, '$response = ');
        
        if(!isset($response) || !is_array($response)){
        	return null;
        }
        
        $children = array();
        $child = null;
        foreach($response as $item){
        	$child = new SynchContentItem(
                                        array(
                                            'id'=>$item[1]['id'],
                                            'name'=>$item[1]['name'],
                                            'description'=>$item[1]['description'],
                                            'sourceServerId'=>$item[1]['sourceServerId']
                                        )
                                    );
                                    
            if(!empty($item[1]['serverIds'][0])){
            	$child->appendServerId($item[1]['serverIds'][0]);
            }
                                    
            $children[] =  $child;                                   
        }
        return $children;
        
    }
    
    public function getRemoteDetailsByDataItemId($dataItemId){
    	$method = 'synch/mnet/synch.php/getRemoteDetailsByDataItemId';
        $parameters = array(array($dataItemId, 'string'));
        $response = $this->callRemoteMethod($method, $parameters);
        if(isset($response)){
        	$response = (object) $response;
        }
        return $response;
    }
    
    /*
     * @method getServerDetailsFromRemoteHost
     * @param synch_modal_Server $server
     * return object
     */
    public function getServerDetailsFromRemoteHost($server){
    	
        $hostId = $server->mnetHostId;
        if(empty($hostId)){
        	return null;
        }
        
        $method = 'synch/mnet/synch.php/getServerDetails';

        $details = $this->callRemoteMethod($method, array(), $server);
        
        if(empty($details)){
        	return null;
        }
        
        return $details;
    }
    
    /*
     * @method retrieveRemoteBackup
     * @param string $dataItemId
     * return bool
     */
    public function callRemoteMethod($method, $parameters, $server = null){
    	
        global $CFG, $SynchServerController;
        require_once $CFG->dirroot.'/mnet/xmlrpc/client.php';
        // For the demo, our 'remote' host is actually our local host.
        $wwwroot = $CFG->wwwroot;
        //$method = 'synch/mnet/synch.php/getBackupById';

        // Get local server.
        $localServer = $SynchServerController->checkAndCreateLocalServer();
        global $Out;
        //$Out->print_r($localServer, '$localServer = ');
        // Cannot continue without a local server
        if(empty($localServer)){
        	return null;
        }
        
        if(empty($server)){
        	
            //$Out->append('Generating default remote server');
            //$server = new synch_modal_Server();
            //$server->mnetHostId = 1020000003;
            $server = $SynchServerController->getRemoteServer();
        }
        
        //$Out->print_r($server, '$server = ');
        // Cannot continue without a remote server to call
        if(empty($server) || synch_empty($server->mnetHostId)){
            return null;
        }
        
        // mnet_peer pulls information about a remote host from the database.
        $mnet_peer = new mnet_peer();
        $mnet_peer->set_wwwroot($wwwroot);
        $mnethostid = $server->mnetHostId;
        $mnet_peer->set_id($mnethostid);
        
        // Create a new request object
        $mnet_request = new mnet_xmlrpc_client();
        
        // Tell it the path to the method that we want to execute
        $mnet_request->set_method($method);
        
        // Set the time out to something decent in seconds
        //$mnet_request->set_timeout(600);
        //set_time_limit(120);
        // Add parameters for your function. The mnet_concatenate_strings takes three
        // parameters, like mnet_concatenate_strings($string1, $string2, $string3)
        // PHP is weakly typed, so you can get away with calling most things strings, 
        // unless it's non-scalar (i.e. an array or object or something).
        foreach($parameters as $param) {
            $mnet_request->add_param($param[0], $param[1]);
        }
        
        // We send the request:
        $mnet_request->send($mnet_peer);
        
        return $mnet_request->response;
    }
    
    /*
     * @method retrieveRemoteBackup
     * @param string $dataItemId
     * return bool
     */
    public function retrieveRemoteBackup($dataItemId, $session){
        
        global $CFG, $SynchSessionController, $SynchServerController;
        
        // Should we update the course or create it. Therefore does it exist locally? 
        $id = SynchContentHierarchy::getIdFromDataItemId($dataItemId);
        if(record_exists('course', 'id', $id)){
            $this->setSessionItemExistsByServerId(array($dataItemId), $SynchServerController->getServerId(), $session);
        }
        else {
        	
        }
        
        $method = 'synch/mnet/synch.php/getBackupById';
        $sessionId = $SynchSessionController->getCurrentSessionid(); 
        // Retrieve a backup from the remote moodle using the data id and a session id
        $parameters = array(array($dataItemId, 'string'),
                        array($CFG->wwwroot, 'string'),
                        array($sessionId, 'string')
                        );
        $fileName = $this->callRemoteMethod($method, $parameters);
        if(!isset($fileName)){
        	return null;
        }

        // If a backup is returned from the remote moodle, move it to the session backups folder
        $moved = $this->moveBackupFromQueueToSession($SynchSessionController->getCurrentSession(), $fileName);
        
        if(!$moved){
            return null;
        }
        return $fileName;
        
    }
    
    /*
     * Create a backup by the id and type passed in
     * @param int id parent id
     * @param int type 
     * return bool
     */
    public function getMnetHosts($excludeIds=null){
        
        global $CFG;
        $exclude = '';
        if(is_array($excludeIds)){
        	$exclude.= ' AND h.id NOT IN('.implode($excludeIds, ',').')';
        }
        // Get the mnet host details 
      $hosts = get_records_sql('  SELECT 
                                    h.id, 
                                    h.wwwroot, 
                                    h.ip_address, 
                                    h.name, 
                                    h.public_key, 
                                    h.public_key_expires, 
                                    h.transport, 
                                    h.portno, 
                                    h.last_connect_time, 
                                    h.last_log_id, 
                                    h.applicationid, 
                                    a.name as app_name, 
                                    a.display_name as app_display_name, 
                                    a.xmlrpc_server_url
                                FROM  
                                    '.$CFG->prefix.'mnet_host h,  
                                    '.$CFG->prefix.'mnet_application a  
                                WHERE 
                                    h.id != \''.$CFG->mnet_localhost_id.'\' AND  
                                    h.deleted = \'0\' AND  
                                    h.applicationid=a.id'.$exclude);

        if (empty($hosts)) {
            $hosts = array();
        }
        return $hosts;
    }
    
    /*
     * @method mergeBackups Create a merge of the local and remote backups
     * @param string $id
     * return bool
     */
    public function moveBackupFromQueueToSession($session, $fileName, $type=0){
    	if(!isset($session) || empty($fileName)){
    		return false;
    	}
        
        
        $pathFrom = $this->createQueueSessionPath($session->id, $type);
        // Check the queue for a session folder. If there is one copy any zip 
        // files inside to the session backups folders
        
        $pathFrom.='/'.$fileName;
        if(!FileSystem::exists($pathFrom, 'f')){
        	return false;
        }
        
        $pathTo = $this->createSessionBackupPath($session);
        $pathTo.='/'.$fileName;
        
        FileSystem::createFoldersFromPath(FileSystem::path_pop($pathTo));
        FileSystem::moveFile($pathFrom, $pathTo);
        
        return true;
    }
    
    /*
     * @method mergeBackups Create a merge of the local and remote backups
     * @param string $id
     * @param string[] $files array of file names to merge
     * @return bool
     */
    public function getSessionMergeFile($sessionBackupPath){
    	
        if(!isset($sessionBackupPath)){
        	return null;
        }
        
        global $CFG;
        
        $mergeSuffix = $CFG->synch->merge_file_suffix;
        // Does the session path have a file in it with the $mergeSuffix
        
        $contents = FileSystem::getFolderContents($sessionBackupPath);
        
        if(!is_array($contents)){
        	return null;
        }
        
        $compare = $CFG->synch->merge_file_suffix.'.zip';
        foreach($contents as $name){
        	if($name == '.' || $name == '..'){
        		continue;
        	}

            if(strpos($name, $compare)){
            	return $name;
            }
        }
        
        return null;
    }
    
    /*
     * @method mergeBackups Create a merge of the local and remote backups
     * @param string $id
     * @param string[] $files array of file names to merge
     * @return bool
     */
    public function mergeBackups($dataItemId, $files, $session){
    	
        global $CFG, $SynchServerController;
        $toMerge = 0;
        
        
        $sessionBackupPath = $this->createSessionBackupPath($session);
        $mergeFileName = $this->getSessionMergeFile($sessionBackupPath);
/*
        // If there is already a merge file created don't create one
        if($mergeFileName = $this->getSessionMergeFile($sessionBackupPath)){
            return $mergeFileName;
        }
        */
        
        $localBackupPath = $sessionBackupPath.'/'.$files['local'];
        $remoteBackupPath = $sessionBackupPath.'/'.$files['remote'];
        // Is there a local backup file
        // if yes $toMerge+=1;
        if(isset($files['local']) && FileSystem::exists($localBackupPath, 'f')){
        	$toMerge+=1;
        }
        
        // Is there a remote backup file
        // if yes $toMerge+=2;
        if(isset($files['remote']) && FileSystem::exists($remoteBackupPath, 'f')){
            $toMerge+=2;
        }
        
        if(empty($toMerge)){
        	return null;
        }
        

        
        // If there is only one file then create the merge from the file
        if(3>($toMerge & 3)){
        	
            if($toMerge & 1){
            	$copyFrom = $localBackupPath;
                
                // Log to the session that changes have been merged
                $this->setSessionHasChangesByServerId(true, $SynchServerController->getServerId(), $session);
            }
            else{
            	$copyFrom = $remoteBackupPath;
                // Log to the session that changes have been merged
                $this->setSessionHasChangesByServerId(true, $SynchServerController->getRemoteServerId(), $session);
            }
            
            $uniqueCode = synch_backup_controller::getUniqueCodeFromFileName($copyFrom);
            $mergeFileName = $this->createMergeFileName($dataItemId);
            // append merge to it instead of the serverid so it can be distinguished as a merge file.
            $mergeFileName = FileSystem::appendToFileName($mergeFileName, '.'.$uniqueCode);
            $mergeFileName = FileSystem::appendToFileName($mergeFileName, '.'.$CFG->synch->merge_file_suffix);
            $files['merge'] = $mergeFileName;
            
            $copyTo = $sessionBackupPath.'/'.$mergeFileName;
            
            // Create the merge file by copying and renaming the file
            FileSystem::copyFile($copyFrom, $copyTo);
                
            return $mergeFileName;
        }
        
        // Otherwise we need to merge the two files
        
        // Ok we haven't written the merge yet so just use one of the files to create the merge file
        $mergeFromLocal = false;
        if($mergeFromLocal){
    	   $copyFrom = $localBackupPath;
           
           // Log to the session that changes have been merged
           $this->setSessionHasChangesByServerId(true, $SynchServerController->getServerId(), $session);
        }
        else{
            $copyFrom = $remoteBackupPath;
            
            // Log to the session that changes have been merged
                $this->setSessionHasChangesByServerId(true, $SynchServerController->getRemoteServerId(), $session);
        }
    
        $uniqueCode = synch_backup_controller::getUniqueCodeFromFileName($copyFrom);
        $mergeFileName = $this->createMergeFileName($dataItemId);
        // append merge to it instead of the serverid so it can be distinguished as a merge file.
        $mergeFileName = FileSystem::appendToFileName($mergeFileName, '.'.$uniqueCode);
        $mergeFileName = FileSystem::appendToFileName($mergeFileName, '.'.$CFG->synch->merge_file_suffix);
        $files['merge'] = $mergeFileName;
            
        $copyTo = $sessionBackupPath.'/'.$mergeFileName;
        
        // Create the merge file by copying and renaming the file
        FileSystem::copyFile($copyFrom, $copyTo);
            
        return $mergeFileName;
            
    }
    
    /*
     * @method createMergeFileName Create the name of the merged backup file
     * @param string $id
     * return string
     */
    public function createMergeFileName($dataItemId){
    
        // Create the file name for the merge.
        // For now we are assuming the $id is the dataItemId which contains the courseId
        $courseId = SynchContentHierarchy::getIdFromDataItemId($dataItemId);
        global $CFG;
        
        // If its's a remote course we won't find the details here. 
        if (!$course = get_record('course', 'id', $courseId)) {
           // We can't find the course Id, there's a fair chance it will be a remote course so lets set the default
           $course = new stdClass;
           $course->id = $courseId;
           $course->shortname = $CFG->synch->course_shortname_default;
        }
        
        $fileName = backup_get_zipfile_name($course);
        return $fileName;
    }
    
    
    
    /*
     * @method restoreMergedBackup Restore the merged backup
     * @param string $id
     * return bool
     */
    public function restoreMergedBackup($dataItemId, $files, $session){
        
        global $CFG;
        
        $page = new stdClass();
        //Move the merged backup file from the session folder to the course folder
         // Move the backups from the course directory into synch/sessions/sessionid/backups with the serverid appended the file name
        // Generate a new file name from the original backup file and the local server id
        //$newFileName = $this->createSessionBackupFileName($preferences);
        $sessionBackupFileName = $files['merged'];
        // Generate the path to the session backup folder
        $sessionBackupPath = $this->createSessionBackupPath($session);
        
        
        
        // Does the merge file exist
        if(!FileSystem::exists($sessionBackupPath.'/'.$sessionBackupFileName, 'f')){
        	return false;
        }
        
        // Move the session merge file to the course backup folder.
        if(1){// fudge for testing in development
        $courseBackupFileName = FileSystem::removeFromFileName($sessionBackupFileName, $CFG->synch->merge_file_suffix);
        $courseBackupFileName = FileSystem::removeFromFileName(
                                                $courseBackupFileName,
                                                 synch_backup_controller::getUniqueCodeFromFileName($sessionBackupFileName)
                                             );

        

        
        // Do we need to update the file name within the preferences. We will be passing 
        // the file name and from then on it shouldn't be required.
        
        $courseId = SynchContentHierarchy::getIdFromDataItemId($dataItemId);
        $courseBackupPath = synch_Backup_controller::createBackupFilePath($courseId);

        // Create the folder structure if necessary
        
        FileSystem::createFoldersFromPath($courseBackupPath);
        $moved = FileSystem::copyFile($sessionBackupPath.'/'.$sessionBackupFileName, 
                                            $courseBackupPath.'/'.$courseBackupFileName);
        }
        else {
        	$moved = true;
        }
        
        if(!$moved){
        	return false;
        }
        
        
        $preferences = new Object();
        $preferences->course_id = $courseId;
        $preferences->backup_name = $courseBackupFileName;
        $preferences->backup_unique_code = synch_backup_controller::getUniqueCodeFromFileName($sessionBackupFileName);
        
        global $SynchServerController;
        // If there are changes from the remote Moodle restore to the local Moodle 
        $remoteServerId = $SynchServerController->getRemoteServerId();
        if($this->getSessionHasChangesByServerId($remoteServerId, $session)){
            // Call the restoreCourse method of SynchBackupController
            $restored = synch_backup_controller::restoreCourse($preferences, $session);
        }
        
        return $restored;
    }
    
    /*
     * Clear the backup folder from the session folder
     */
    public function clearSessionBackups($session){
    	
        $sessionBackupPath = $this->createSessionBackupPath($session);
        FileSystem::deleteFolder($sessionBackupPath);
        
    }
    
    public function restoreMergedBackupToRemoteHost($dataItemId, $files, $session){
    	
        $sessionBackupFileName = $files['merged'];
        // Generate the path to the session backup folder
        $sessionBackupPath = $this->createSessionBackupPath($session);
        
        
        
        // Does the merge file exist
        if(!FileSystem::exists($sessionBackupPath.'/'.$sessionBackupFileName, 'f')){
            return false;
        }
        
        global $SynchServerController;
        $remoteServerId = $SynchServerController->getRemoteServerId();
        $hasChanges = $this->getSessionHasChangesByServerId($SynchServerController->getServerId(), $session);
        
        // If there are changes from the local Moodle restore to the remote Moodle 
        if(!$this->getSessionHasChangesByServerId($SynchServerController->getServerId(), $session)){
        	return false;
        }
            
        $path = $sessionBackupPath.'/'.$sessionBackupFileName; // Merge file path
        $hostUrl = $SynchServerController->getHostUrlByServerId($remoteServerId);
        $url = $hostUrl.'/synch/queue.php';

        // Upload the merge file to the remote server
        $this->uploadFileToRemoteHost($path, $url);
        // trigger the restore process on the remote server.
        // The method is called restoreMergedBackupFromRemoteHost and should include
        // move from queue to session
        // move from session to course and remove unique code and server id.
        
        // The method must be a web service method. 
        global $CFG, $SynchSessionController;
        $method = 'synch/mnet/synch.php/restoreMergedBackupFromRemoteHost';
        
        $sessionId = $SynchSessionController->getCurrentSessionid(); 
        // Retrieve a backup from the remote moodle using the data id and a session id
        $parameters = array(array($dataItemId, 'string'),
                        array($CFG->wwwroot, 'string'),
                        array($sessionId, 'string'),
                        array($sessionBackupFileName, 'string')
                        );
        return $this->callRemoteMethod($method, $parameters);
        
    }
    public function restoreMergedBackupFromRemoteHost($dataItemId, $hostUrl, $sessionId, $fileName){
    	// Restore session with session id
        global $SynchSessionController, $SynchServerController;
        $session = $SynchSessionController->createSessionAsCurrent(true, array('id'=>$sessionId));
        $session->servers = new stdClass;
        $session->servers->{$SynchServerController->getServerId()} = new stdClass; 
        $session->servers->{$SynchServerController->getRemoteServerId()} = new stdClass; 
        
        $this->moveBackupFromQueueToSession($session, $fileName, 0);
        
        $files = array();
        $files['merged'] = $fileName; 
        
        $sessionBackupPath = $this->createSessionBackupPath($session);
        $path = $sessionBackupPath.'/'.$fileName;
        
        if(!FileSystem::exists($path, 'f')){
        	return false;
    }
        $remoteServerId = $SynchServerController->getRemoteServerId();
        $this->setSessionHasChangesByServerId(true, $remoteServerId, $session);
        $this->setSessionItemExistsByServerId(array($dataItemId), $SynchServerController->getServerId(), $session);
        return $this->restoreMergedBackup($dataItemId, $files, $session);

    }

	function recordChange($tableRowId=0, $action=0, $tableId=null, $moduleId=null) {

		global $CFG, $SITE;
		if(!($moduleId && $tableId && $tableRowId && $action)){
			// Raise an error as insufficent data has been passed
			return false;
		}

		$data = new synch_data();
		$data->setServerId(1);
		$data->setModuleId($moduleId);
		$data->setTableId($tableId);
		$data->setTableRowId($tableRowId);
		$data->setLastUpdated($this->getCurrentTime());
		$data->setAction($action);
		//echo "synch_Synch_controller.recordChange: \$tableRowId (1) = ".$tableRowId."<br />";
		//echo "synch_Synch_controller.recordChange: \$data->getLastUpdated() (1) = ".$data->getLastUpdated()."<br />";
		$id = insert_record($this->tables["synch"], clone($data->getData()));
		if(!$id){
			return false;
		}
		$data->setId($id);
		return true;
		break;

	}

	function getGuid($serverId=null, $moduleId=null, $tableId=null, $tableRowId=0){
		global $CFG;
		$sql = "SELECT guid FROM ".$CFG->prefix.$this->tables["synch"]." WHERE serverId='$serverId' ";
		$sql .= "AND moduleId='$moduleId' AND tableId='$tableId' AND tableRowId='$tableRowId' AND NOT isnull(guid)";
	//	echo "synch_Synch_controller.getGuid: \$sql = ".$sql."<br />";
		return get_field_sql($sql);

	}

	function getIdByGuid($guid=null){
			if(!guid){
				return null;
			}
			global $CFG;
			$sql = "SELECT id FROM ".$CFG->prefix.$this->tables["synch"]." WHERE guid='$guid'";
			//echo "synch_Synch_controller.getIdByGuid: \$sql = ".$sql."<br />";
			return get_field_sql($sql);

	}

	function createGuid($serverId=null, $tableId=null, $moduleId=null, $tableRowId=0){
			if(!($moduleId && $tableId && $tableRowId && $serverId)){
				return null;
			}

			return $serverId.$moduleId.$tableId.$tableRowId.$this->getCurrentTime();
	}

	function createGuidFromData($data){
		if(!($data->getModuleId() && $data->getTableId() && $data->gettableRowId() && $data->getServerId())){
			return null;
		}

		return $data->getServerId().
							$data->getModuleId().
							$data->getTableId().
							$data->gettableRowId().
							$this->getCurrentTime();
	}

	function getCurrentTime(){
		$time = gettimeofday();// Not all operating systems support this function.
		return $time["sec"].$time["usec"];
	}
	
	function getDatabaseByReference($reference){
		//return $this->databases[$reference];
		global $CFG;
		return $CFG->synch->databases->{$reference}->instance;
	}
	
	function setDatabaseByReference($reference, $database){
		//$this->databases[$reference] = $database;
		global $CFG;
		$CFG->synch->databases->$reference->instance = $database;
		global $Out;
		//$Out->type($CFG->synch->databases->client->instance, "\$CFG->synch->databases->client->instance = .");
	}
	
	function getDatabaseConfigByReference($reference){
		//return $this->databases[$reference];
		global $CFG;
		return $CFG->synch->databases->{$reference};
	}
	
	function performImport(){
		// Raise an error as the perform import method must be overridden
		return false;
	}
	
	function getRecordsToSynch($moduleId=null){
		
		// Get the last modified date
		
		synch_set_database($this->getDatabaseByReference('master'));
		$where = '';
		if($moduleId){
			$where.="WHERE ";
			$where.="moduleId=$moduleId";
		}
		
		global $Out;
		
		$records = get_records_sql("SELECT * FROM mdl_synch".$where);
		$synchData;
		$record;
		$newRecords = array();
		$Out->print_r($records, "\$records = ");
		//$Out->flush();
		foreach ($records as $record) {
			$synchData = new synch_data();
			//$record = $records[$index];
			$synchData->updateData($record);
		//	$synchData->output("getRecordsToSynch: just synched");
			$newRecords[] = $synchData;
		}
		
	//	$Out->flush();
		synch_set_default_database(); //Always reset the db connection to the default when leaving a method.
		//synch_set_database($this->getDatabaseByReference('slave'));
		
		return $newRecords;
	}
	
	function getRecordsToSynchFromMaster($tableId, $primaryKey="id", $where=null){
		
		if(!isset($primaryKey)){
			$primaryKey="id";
		}
		//Prepare a synch record
    	$baseRecord = new synch_data(); 
    	$baseRecord->setTableId($tableId);
    	$baseRecord->setAction(3);
    	
    	// Get the ids 
    	$ids = $this->retrieveFieldFromMasterByRecord($primaryKey, $baseRecord, $where);
    	$records = array();
    	
    	$record = null;
    	// Create a list of records from the ids
    	for($i=0;$i<count($ids);$i++){
    		$record = clone($baseRecord);
    		$record->setTableRowId($ids[$i]->id);
    		$records[] = $record;
    	}

    	return $records;
	}
	
	function setDatabase($reference){
		if($reference=='default'){
			synch_set_default_database();	
			return;	
		}	
	
		synch_set_database($this->getDatabaseByReference($reference));
		
		$this->setCurrentDatabase($this->getDatabaseByReference($reference));
	}
	
	function setCurrentDatabase(&$database){
		$this->currentDatabase = $database;
	}
	
	function &getCurrentDatabase(){
		return $this->currentDatabase;
	}
	
	function getPrefixOfCurrentDatabase(){
		return $this->getCurrentDatabase()->prefix;
	}
	
	function getDatabasePrefixByReference($reference){
		if(!$this->getDatabaseConfigByReference($reference)){
			return '';
		}
	
		return $this->getDatabaseConfigByReference($reference)->prefix;
	}
	

	function importSimpleRecordToDB($action, $bean, $record, $type){
    	// Import the post data from the master database.
    	//$record->output();
    	//$action = $this->determineAction($record->getAction(), $record->getTableId(), $bean);
    	
     	$beanName = '';
     	if(method_exists($bean, 'getName()')){
     		$beanName = $bean->getName();
     	}
    	global $Out;
    	
    	// Update the data into the local database.
    	switch($action){
       		case synch_Synch_controller::$ACTION_CREATE:  
       			//Check if the post already exists    
    			if( record_exists($record->getTableId(), "id", $record->getTableRowId())){
    				
    				if($this->getDebug()){
		    			$this->appendToDebug("&nbsp;&nbsp;&nbsp;The $type record '{$beanName}' id({$bean->getId()}) was not added as it already exists. ");
    				}
		    		return false;
		    	}   	
		    	$primaryKeyProvided = true;
		    	$generateUniqueId = false;

				$data = $this->prepareBeanDataForDB($bean->getData());
				
				/*
				if($record->getTableId()==user_synch_manager::$TABLE_USER_ROLE_ASSIGNMENT){
		    		global $Out;
		    		$Out->append("\$action = $action");
		    		$Out->append("\$primaryKeyProvided = $primaryKeyProvided");
		    		$Out->print_r($data, "\$data = ");
		    		$Out->flush();
		    	}
		    	*/

		    	if(!insert_record($record->getTableId(), $data, true, 'id', $primaryKeyProvided, $generateUniqueId)){
		    		if($this->getDebug()){
		  	  			$this->appendToDebug("&nbsp;&nbsp;&nbsp;The $type record '{$beanName}' id({$bean->getId()})was not added. ");
		    		}
		    		return false;
		    	}
		    	if($this->getDebug()){
		    		$this->appendToDebug("&nbsp;&nbsp;&nbsp;The $type record '{$beanName}' id({$bean->getId()})was added. ");
		    	}

		    	return true;
		    	break;
		    
		    case synch_Synch_controller::$ACTION_UPDATE:
		    	//Check if the post already exists    
    			if(!record_exists($record->getTableId(), "id", $record->getTableRowId())){
    				if($this->getDebug()){
    			  		$this->appendToDebug("&nbsp;&nbsp;&nbsp;The $type record '{$beanName}' id({$bean->getId()})was not updated as it doesn't exist. ");
    				}
		    		return false;
		    	}  
		    	$data = $this-> prepareBeanDataForDB($bean->getData());
		    	update_record($record->getTableId(), $data);
		    	if($this->getDebug()){
		    		$this->appendToDebug("&nbsp;&nbsp;&nbsp;The $type record '{$beanName}' id({$bean->getId()})was updated. ");
		    	}
		    	
		    	return true;
		    	break;
		    	
		    case synch_Synch_controller::$ACTION_DELETE:
		    	//Check if the post already exists    
 
		    	delete_records($record->getTableId(), "id", $record->getTableRowId());
		    	$this->appendToDebug("&nbsp;&nbsp;&nbsp;The $type record '{$beanName}' id({$record->getTableRowId()})was deleted. ");
		    	
		    	return true;
		    	break;
    	}
    }
    
    function prepareBeanDataForDB($data){
    	
    	foreach($data as $key => $value){
    		$data->$key = addslashes($value);
    	}
    	
    	return $data;
    }
    
    function retrieveRecordFromMaster($record, $type, $BeanFactory){
    	global $CFG;
		global $Out;  
		//$record->output(); 
    	$this->setDatabase('master');
    	$sql = "SELECT * FROM ".$this->getDatabasePrefixByReference('master').$record->getTableId()." WHERE id=".$record->getTableRowId();
    //	$Out->append("\$sql = $sql");
    	$data = get_record_sql($sql);
    	$this->setDatabase('default');
    	
    	// Create a bean
		$bean = $BeanFactory->createObject($type);
		$bean->updateData($data);
    	return $bean;
    }
    
    /*
     * Retrieve a column from the master database and return values as an array.
     */
    function retrieveFieldFromMasterByRecord($field, $record, $where=''){
    	global $CFG;
    	$this->setDatabase('master');
    	$sql = "SELECT $field FROM ".$this->getDatabasePrefixByReference('master').$record->getTableId();
    	if(isset($where)){
    		$sql.=" WHERE ".$where;
    	}

    	$data = get_records_sql($sql);
    	$this->setDatabase('default');
    	
    	global $Out;
    	$Out->append('$sql = '.$sql);
    	$Out->print_r($data, '$data = ');
    	
    	$rows = array();
    	if(!$data){
    		return $rows;
    	}
    	
    	foreach($data as $row => $value){
			$rows[] = $value;
    	}
    	return $rows;
    }
    
    function retrieveRecordsFromMaster($record, $type, $BeanFactory){
    	global $CFG;
    	$this->setDatabase('master');
    	$sql = "SELECT * FROM ".$this->getDatabasePrefixByReference('master').$record->getTableId();
    	$data = get_records_sql($sql);
    	$this->setDatabase('default');
    	
    	$records = array();
    	foreach($data as $record){
	    	// Create a bean
			$bean = $BeanFactory->createObject($type);
			$bean->updateData($record);
			$records[] = $bean;
    	}
    	return $records;
    }
    
    function refreshRecordsFromMaster($records, $tableId, $type){
    	global $Out;
    	
    	// Clear records in local table
    	delete_records($tableId);
    	
    	//Insert the records 	
    	if($this->getDebug()){
    		$this->appendToDebug("Importing ".count($records)." {$type->getDescription()} record(s) for {$tableId}");
    	}
    	
    	$primaryKeyProvided = true;
    	$generateUniqueId = false;
    	$bean = null;
    	$beanName = '';
    	for($i=0; $i<count($records);$i++){
    		$bean = $records[$i];
    		$beanName = '';
	     	if(method_exists($bean, 'getName()')){
	     		$beanName = $bean->getName();
	     	}
	    	if(!insert_record($tableId, $bean->getData(), true, 'id', $primaryKeyProvided, $generateUniqueId)){
	    		if($this->getDebug()){
	  	  			$this->appendToDebug("&nbsp;&nbsp;&nbsp;The $type record '{$beanName}' id({$bean->getId()})was not added. ");
	    		}
	    		return false;
	    	}
	    	if($this->getDebug()){
	    		$this->appendToDebug("&nbsp;&nbsp;&nbsp;The {$type->getDescription()} record '{$beanName}' id({$bean->getId()})was added. ");
	    	}
    	}
    	
    	
    }
    
    function getSimpleChildRecordAndImportToDB($record, $parentTableId, $type){
	
    	// Import the forum data from the master database.
    	$bean = $this->retrieveRecordFromMaster($record, $type->getName());
		$action = $this->determineAction(
								$record->getAction(), 
								$parentTableId, 
								$bean
							);
							
    	// Translate any contextIds from the master value to the client value
    	if(method_exists($bean, 'getContextId') && $bean->getContextId()){
    		$bean->setContextId($this->translateContextIdFromRemoteToLocal($bean->getContextId()));
    	}
    	$this->importSimpleRecordToDB($action, $bean, $record, $type->getDescription());
		return true;
    }
    
    /*
     * This method is designed to determine the correct contentId to use for the
     * local server given the contextId of the remote server. If the context
     * doesn't exist locally then it must be created from the remote values.
     * 
     * @contextId: contestId of remote server
     */
    function createCacheOfRemoteContext(){
        	
    	global $CFG;

		// Get the context from the remote server using the contextId  
    	$this->setDatabase('master');
    	$sql = "SELECT * FROM ".$this->getDatabasePrefixByReference('master')."context";
    	$context = get_records_sql($sql);
    	$this->setDatabase('default');
     	$this->setRemoteContextCache($context);
    }
    
    function getRemoteContextCache(){
    	return $this->cache->remoteContext;
    }
    
    function setRemoteContextCache($new){
    	$this->cache->remoteContext = $new;
    }
    
    function getContextFromRemoteCacheByContextId($contextId){
    	if(!isset($this->cache->remoteContext) || !isset($this->cache->remoteContext[$contextId])){
    		return null;		
       	}
       	
       	return $this->cache->remoteContext[$contextId];
    }
    
    /*
     * This method is designed to determine the correct contentId to use for the
     * local server given the contextId of the remote server. If the context
     * doesn't exist locally then it must be created from the remote values.
     * 
     * Note: For best p[erformance we need to create an object to represent
     * a look up table which maps the remote context to the local context. Thus
     * this could easily be kept up to date as new contexts are added as
     * appropriate.
     * 
     * @contextId: contestId of remote server
     */
    function translateContextIdFromRemoteToLocal($contextId){
        	
    	global $CFG;

		// Get the context from the remote server using the contextId  
		$remoteContext = $this->getContextFromRemoteCacheByContextId($contextId);
		
		if(!isset($remoteContext) || !isset($remoteContext->id)){
			return false;
		}
		
 	   	// Check if a matching context can be found in the local database
    	$sql = "SELECT * FROM ".$this->getDatabasePrefixByReference('client')."context WHERE contextlevel=".$remoteContext->contextlevel." ";
    	$sql.="AND instanceid=".$remoteContext->instanceid." ";
    	$localContext = get_record_sql($sql);
    	
    	if(isset($localContext) && isset($localContext->id)){
			return $localContext->id;
		}
    	
    	// The context was not found locally so it must be added.
    	$remoteContextClone = clone($remoteContext);
    	unset($remoteContextClone->id);
    	return $this->createLocalContextId($remoteContextClone);
    }
    
    function createLocalContextId($context){
    	return insert_record("context", $context, true);
    }
    
    function determineAction($action, $field, $bean){

       	//If $action is 3 then we need to determine the specific action. Update or Create
    	if($action == synch_Synch_controller::$ACTION_UPDATE){
			// Is it a new record or an update to an existing one?
			if(!$id = get_field($field, 'id', 'id', $bean->getId())){
				return synch_Synch_controller::$ACTION_CREATE;
			}
			else{
				return synch_Synch_controller::$ACTION_UPDATE;
			}
    	}
    	
    	return $action;
    }
    
    function setDebug($level){
    	$this->debug = $level;
    }
    
    function getDebug(){
    	return $this->debug;
    }
    
    function appendToDebug($text){
    	if(!$this->getDebug()){
    		return;
    	}
    
    	echo $text."<br />";
    }
    
    //helper methods
    function getType($typeName){
    	return $this->types->getConfiguration($typeName);
    }
 }
?>