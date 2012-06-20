<?php

 /*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * The synchronisation process is underpinned by sessions. The Session
 * controller class provides the necessary methods to access and manage
 * sessions.
 */

 class synch_Session_controller{
 	/*
     * @param array $sessions 
     * an array of session objects referenced by session id
 	 */
    
    protected $sessions = array();
    
    /*
     * Stores the current session object
     */
    protected $currentSession = null;
    
    /*
     * Return the current session object
     * @method getCurrentSession
     * @return synch_Session
     */
    public function getCurrentSession(){
    	return $this->currentSession;
    }
    
    /*
     * Set the current session object
     * @method setCurrentSession
     * @param synch_Session $new
     * @return void
     */
    public function setCurrentSession($new){
        $this->currentSession = $new;
    }
    
    /*
     * Return the current session object session id
     * @method getCurrentSessionId
     * @return string
     */
    public function getCurrentSessionId(){
        return $this->currentSession->id;
    }
    
    /*
     * Retrieve a session object from it's session id and set it as the current
     * session object
     * @method setCurrentSessionById
     * @param string $id
     * @return bool
     */
    public function setCurrentSessionById($id){
        $session = new synch_Session(array('id'=>$id));
        if(!isset($session)){
        	return false;
        }
        $this->currentSession = $session;
        return true;
    }
    
    /*
     * Create a new session object and return it
     * @method createSession
     * @return synch_Session
     */
    public function createSession($properties = null){
        
        if(!isset($properties)){
        	$properties = array();
        }
        if(!isset($properties['id'])){
            $properties['id'] = uuid::getUniqueID();
        }
        $session = new synch_Session($properties);
        return $session;
    }
    
    /*
     * Create a new session object and return it
     * @method createSession
     * @return mixed
     */
    public function createSessionAsCurrent($returnSession=false, $properties = null){
        $session = $this->createSession($properties);;
        $this->setCurrentSession($session);
        if($returnSession){
        	return $session;
        }
        
        return true;
    }
    
    
    /*
     * Create a new session object and return it
     * @method createSession
     * @return mixed
     */
    public function createSessionFromSessionId($sessionId, $makeCurrent=false){
        
        $properties = null;
        if(!empty($sessionId)){
        	$properties = array('id'=>$sessionId);
        }
        
        $session = $this->createSession($properties);
        if($makeCurrent){
            $this->setCurrentSession($session);
        }
        
        return $session;
        
    }
    
    /*
     * Return a path to a session folder given a session object
     * @method createSessionPath
     * @param synch_Session $session 
     * @return string
     */
    public static function createSessionPath($session){
        if(!isset($session)){
            return null;
        }
        
        GLOBAL $CFG;
        $sessionPath = $CFG->synch->dataroot.'/sessions/'.$session->id;
        return $sessionPath;
    }
    
    /*
     * @method restoreMergedBackup Restore the merged backup
     * @param string $id
     * return bool
     */
    public static function serializeToSessionFolder($object, $filePath, $session){
        
        if(!isset($object)){
            return false;
        }
        
        
        FileSystem::putFileContents(self::createSessionPath($session).'/'.$filePath,serialize($object),true, true);
        
        return true;
    }
    
    /*
     * @method restoreMergedBackup Restore the merged backup
     * @param string $id
     * return bool
     */
    public static function unSerializeFromSessionFolder($filePath, $session){
        
        if(!isset($filePath) || !isset($session)){
            return null;
        }
        
        
        $contents = FileSystem::getFileContents(self::createSessionPath($session).'/'.$filePath);
        
        if(!isset($contents)){
        	return null;
        }
        
        $object = unserialize($contents);
        return $object;
    }
    
    public static function saveSession($session){
    	
        $name = 'session.txt';
        return self::serializeToSessionFolder($session, $name, $session);     
        
    }
    
    public static function loadSession($sessionId, $session=null){
        
        if(empty($session)){
        	GLOBAL $SynchSessionController;
            $session = $SynchSessionController->createSessionFromSessionId($sessionId, true);
        }
        $name = 'session.txt';
        $savedSession = self::unSerializeFromSessionFolder($name, $session);
        if(empty($savedSession)){
        	return $session;
        }  
        
        return  $savedSession; 
        
    }
 }
?>
