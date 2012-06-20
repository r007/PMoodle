<?php
 
 /*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * In time this class will provide simple methods to manage each server that
 * Offline Moodle is capable of connecting to
 */
 
 class synch_Server_controller{
    
    /*
     * Stores the current server object
     */
    protected $currentServer = null;
    
    /*
     * Store the remote server id for quick reference. It time the server
     * controller should handle this.
     * @var int $remoteServerId
     */
    protected $remoteServerId = null;
    
    /*
     * Details of the trusted servers are stored here for easy use. Contains an
     * array of server objects referenced by server id
     * @var array $servers
     */
    protected $servers = array();
    
    /*
     * Return the current server object
     * @method getCurrentServer
     * @return synch_Server
     */
    public function getCurrentServer(){
    	return $this->currentServer;
    }
    
    /*
     * Set the current server object
     * @method setCurrentServer
     * @param synch_modal_Server $new
     * @return void
     */
    public function setCurrentServer($new){
        $this->currentServer = $new;
    }
    
    /*
     * Return the current server object server id
     * @method getCurrentServerId
     * @return string
     */
    public function getCurrentServerId(){
        return $this->currentServer->id;
    }
    
    /*
     * Retrieve a server object from it's server id and set it as the current
     * server object
     * @method setCurrentServerById
     * @param string $id
     * @return bool
     */
    public function setCurrentServerById($id){
        $server = new synch_modal_Server(array('id'=>$id));
        if(!isset($server)){
        	return false;
        }
        $this->currentServer = $server;
        return true;
    }
    
    /*
     * Create a new server object and return it
     * @method createServer
     * @return synch_modal_Server
     */
    public function createServer($properties = null){
        
        if(!isset($properties)){
        	$properties = array();
        }
        if(!isset($properties['id'])){
            //$properties['id'] = uuid::getUniqueID();
        }
        $server = new synch_Server($properties);
        return $server;
    }
    
    /*
     * Create a new server object and return it
     * @method createServer
     * @return mixed
     */
    public function createServerAsCurrent($returnServer=false, $properties = null){
        $server = $this->createServer($properties);;
        $this->setCurrentServer($server);
        if($returnServer){
        	return $server;
        }
        
        return true;
    }
    
    
    /*
     * Create a new server object and return it
     * @method createServer
     * @return mixed
     */
    public function createServerFromServerId($serverId, $makeCurrent=false){
        
        $properties = null;
        if(!empty($serverId)){
        	$properties = array('id'=>$serverId);
        }
        
        $server = $this->createServer($properties);
        if($makeCurrent){
            $this->setCurrentServer($server);
        }
        
        return $server;
        
    }
    
    /*
     * Return a path to a server folder given a server object
     * @method createServerPath
     * @param synch_Server $server 
     * @return string
     */
    public static function createServerPath($server){
        if(!isset($server)){
            return null;
        }
        
        GLOBAL $CFG;
        $serverPath = $CFG->synch->dataroot.'/servers/'.$server->id;
        return $serverPath;
    }
    
    /*
     * @method restoreMergedBackup Restore the merged backup
     * @param string $id
     * return bool
     */
    public static function serializeToServerFolder($object, $filePath, $server){
        
        if(!isset($object)){
            return false;
        }
        
        
        FileSystem::putFileContents(self::createServerPath($server).'/'.$filePath,serialize($object),true, true);
        
        return true;
    }
    
    /*
     * @method restoreMergedBackup Restore the merged backup
     * @param string $id
     * return bool
     */
    public static function unSerializeFromServerFolder($filePath, $server){
        
        if(!isset($filePath) || !isset($server)){
            return null;
        }
        
        
        $contents = FileSystem::getFileContents(self::createServerPath($server).'/'.$filePath);
        
        if(!isset($contents)){
        	return null;
        }
        
        $object = unserialize($contents);
        return $object;
    }
    
    
    /* Each moodle instance has a server id. Return the id for this instance
     * 
     * This method should be moved to the synch server controller
     * @method getServerId 
     * @uses $CFG
     * @return string
     */
    public function getServerId(){
        global $CFG;
        if(empty($CFG->synch_server_id)){
        	return null;
        }
        return $CFG->synch_server_id;
    }
    
    /* 
     * Each moodle instance has a server id. Get the id of the remote server
     * stored in this class.
     * 
     * This method should be moved to the synch server controller
     * @method getRemoteServerId 
     * @return string
     */
    public function getRemoteServerId(){
        return $this->remoteServerId;
    }
    
    /* 
     * Each moodle instance has a server id. Set the id of the remote server
     * stored in this class.
     * 
     * This method should be moved to the synch server controller
     * @method setRemoteServerId 
     * @param string $new
     * @return void
     */
    public function setRemoteServerId($new){
        $this->remoteServerId = $new;
    }
    
    /* 
     * Return the array of synch_modal_Server objects currently set
     * 
     * This method should be moved to the synch server controller
     * @method getServers 
     * @return array synch_modal_Server
     */
    public function getServers(){
        return $this->servers;
    }
    
    /* Set the array of synch_modal_Server objects
     * 
     * This method should be moved to the synch server controller
     * @method setServers
     * @param array $new
     * @return void
     */
    public function setServers($new){
        $this->servers = $new;
    }
    
    /*
     * Return a server instance from the servers array using the given server id
     * 
     * This method should be moved to the synch server controller
     * @method getServerByServerId
     * @param string $id
     * @return synch_modal_Server
     */
    public function getServerByServerId($id){
        if(empty($this->servers[$id])){
            return null;
        }
        return $this->servers[$id];
    }
    
    /*
     * Append a server to the server array
     * 
     * This method should be moved to the synch server controller
     * @method appendServer
     * @param synch_modal_Server $server
     * return void
     */
    public function appendServer($server){
        $this->servers[$server->serverId] = $server;
    }
    
    
    /*
     * Return the host url of a server in the servers array given its id
     * 
     * This method should be moved to the synch server controller
     * @method getHostUrlByServerId
     * @param string $id
     * @return string
     */
    public function getHostUrlByServerId($id){

        if(empty($this->servers[$id]) || $this->servers[$id]->wwwroot==''){
            return null;
        }
        
        return $this->servers[$id]->wwwroot;
    }
    
    /*
     * To save a call to the server itself we can use a dataItemId to retrieve
     * the servers id. 
     * 
     * This method should be moved to the synch server controller
     * @method getServerIdFromId
     * @param dataItemId $id
     * @return string
     */
    public function getServerIdFromId($id){
        if(empty($id)){
            return null;
        }
        
        return substr($id, 0, 3);
    }
    
    /*
     * Return the object representing the local server from the servers array
     * using the local server id
     * 
     * This method should be moved to the synch server controller
     * @method getLocalServer
     * @return synch_modal_Server
     */
    public function getLocalServer(){
        return $this->getServerByServerId($this->getServerId());
    }
    
    /*
     * Return the object representing the remote server from the servers array
     * using the remote server id
     * 
     * This method should be moved to the synch server controller
     * @method getRemoteServer
     * @return synch_modal_Server
     */
    public function getRemoteServer(){
        $serverId = $this->getRemoteServerId();
        return $this->getServerByServerId($serverId);
    }
    
    /*
     * Convenience method to intialise both the local and remote server to use
     * 
     * This method should be moved to the synch server controller
     * @method createAndAppendDefaultServers
     * @return void
     */
    public function createAndAppendDefaultServers(){
        
        global $CFG, $SynchManager;
        
        // Create local and remote server objects and store them in this class
        $server = new synch_modal_Server();
        $server->serverId = $this->getServerId();
        $server->wwwroot = $CFG->wwwroot;
        $this->appendServer($server);
        
        // Remote Server
        $server = new synch_modal_Server();
        $serverId = $this->getRemoteServerId();
        $details = $SynchManager->getServerDetailsFromDBByServerId($serverId);
        $server->fromObject($details);
        $this->appendServer($server);
    }
    
    /*
     * Return the local server object. Create it if it doesn't exist.
     * 
     * This method should be moved to the synch server controller
     * @method checkAndCreateLocalServer
     * @return void
     */
    public function checkAndCreateLocalServer(){
        
        $server = $this->getLocalServer();
        if(!empty($server)){
            return $server;
        }
        
        global $CFG;
        $server = new synch_modal_Server();
        $server->serverId = $this->getServerId();
        $server->wwwroot = $CFG->wwwroot;
        $this->appendServer($server);
        
        return $server;
    }
 }
?>
