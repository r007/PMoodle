<?php
/*
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch.lib
 * 
 * This file contains a class definition for the manipulation of file system
 * objects such as files, directories and drives.Extends the methods providing
 * in /lib/filelib.php and aims to provide reusable and practical file system
 * hanlding methods.
 * 
 * In php there is no specific file system object so this class is designed to
 * encapsulate file related methods into one class.
 */
 
// ---------------------------------------------------
// Constructor / Destructor
// ---------------------------------------------------

/**
 * @name FileSystem
 * Class providing simple reusable methods for file system actions.
 */
class FileSystem {

    /*
     * Standard path separator to be used in creating file paths.
     * @var $_pathSeparator
     * /
	protected $_pathSeparator = '/';

	/**
	 * ?
	 */
	function __destruct() {

	}

	// ---------------------------------------------------
	// General Functions
	// ---------------------------------------------------

	function getPathSeparator(){
		return $this->_pathSeparator;
	}
	
	function setPathSeparator($new){
		$this->_pathSeparator = $new;
	}
	
    /*
     * Does a file or folder exist on the file system
     * @method exists
     * @param string $filePath path to check
     * @param string $type cehck for 'f' a folder, 'd' a document or 'e' either
     * @return bool
     */
	public static function exists($filePath, $type='e'){

		/*
		 * Php does not check for files and folders separately so the checks
		 * have been amalgamated.
		 */

		switch($type) {
			case 'd':
				return is_dir($filePath);
				break;
			case 'f':
				return file_exists($filePath);
				break;
			case 'e':
				if(file_exists($filePath)){
					return true;
				}
				
				if(is_dir($filePath)){
					return true;
				}
				break;
		}

		// No type was matched
		return false;
	}


	/**
     * Read a file from the file system and return its contents
	 * @method readBinaryFile
     * @param string $filePath path to check
     * @return string file contents
	 */
	public static function readBinaryFile($filePath) {

		return file_get_contents($filePath);

	}

	/*
     * Rename a file on the file system
     * @method renameFile
	 * @param string $old path to file
	 * @param string $new path to change to
	 * @return bool indicates success or failure
	 */
	public static function renameFile($old, $new){
		return rename($old, $new);	
	}
	
    /*
     * Move a file on the file system
     * @method moveFile
     * @param string $from path to move file from
     * @param string $to path to move file to
     * @return bool indicates success or failure
     */
	public static function moveFile($from, $to){
        /*
         * php doesn't have a move method so copy to the destination and the
         * deleting the original is the method to use
         */
        copy($from, $to);
		unlink($from);// delete the file

		return true;
	}
	
    /*
     * Copy a file on the file system
     * @method copyFile
     * @param string $from path to copy file from
     * @param string $to path to copy file to
     * @return bool indicates success or failure
     */
	public static function copyFile($from, $to){
		return copy($from, $to);
	}
	
    /*
     * Get the contents of a file
     * @method getFileContents
     * @param string $path path to file 
     * @return string file contents
     */
	function getFileContents($path){
		if(!$exists = self::exists($path)){
			return null;
		}
        return file_get_contents($path);
	}

    /*
     * Create a hierarchy of folders from the path passed in. This is a
     * convenience method to ensure a full path is valid instead of checking
     * each folder in turn.
     * @method createFoldersFromPath
     * @param string $path path to file 
     * @return bool indicates success or failure
     */
	public static function createFoldersFromPath($path){

		//Does the current folder exist
		if(!self::exists($path, "d")){
			// Create the parent folder
			if(!self::createFoldersFromPath(self::path_pop($path))){
				return false;
			}
		}

		return self::createFolder($path);
	}

    /*
     * Create a path string that follows the unix standard of / from path
     * separator. This is a convenience method to encorage the use of standard
     * path references to avoid the \\ and / differences.
     * @method normalisePath
     * @param string $path path to file 
     * @return bool indicates success or failure
     */
	public static function normalisePath($path){
		if(empty($path)){
			return null;
		}

		return str_replace(array("\\"), "/", $path);
	}

    /*
     * Create an array from a path split on the path separator. This is a
     * convenience method for quick manipulation of paths 
     * @method convertPathToArray
     * @param string $path path to file 
     * @return array
     */
	public static function convertPathToArray($path){
		return explode("/", $path);
	}

    /*
     * Remove the last element of a path. This is a convenience method to
     * quickly reduce a path by one level
     * @method path_pop
     * @param string $path path to file 
     * @return string new path
     */
	public static function path_pop($path){

		$array = self::convertPathToArray($path);
		if(!is_array($array) || !count($array)){
			return null;
		}

		array_pop($array);
		return implode("/", $array);
	}
	
    /*
     * Append a new element to the end of a path. This is a convenience method
     * to quickly increase a path by one level
     * @method path_push
     * @param string $path path to file 
     * @param string $value value to add
     * @return string new path
     */
	public static function path_push($path, $value){

		$array = self::convertPathToArray($path);
		if(!is_array($array) || !count($array)){
			return null;
		}

		array_push($array, $value);
		return implode("/", $array);
	}
	
    /*
     * Retrieve the folder name at the end of a path. This is a convenience
     * method identical to getCurrentFileName
     * @method getCurrentFolderName
     * @param string $path path to file 
     * @return string folder name
     */
	public static function getCurrentFolderName($path){
		$array = self::convertPathToArray($path);
		if(!is_array($array) || !count($array)){
			return null;
		}

		return $array[count($array)-1];
	}
	
    /*
     * Retrieve the file name at the end of a path. This is a convenience method
     * identical to getCurrentFolderName
     * @method getCurrentFileName
     * @param string $path path to file 
     * @return string file name
     */
	public static function getCurrentFileName($path){
		$array = self::convertPathToArray($path);
		if(!is_array($array) || !count($array)){
			return null;
		}

		return $array[count($array)-1];
	}
	
    /*
     * Retrieve the file extension at the end of a path. 
     * @method getExtension
     * @param string $path path to file 
     * @return string file extension
     */
	public static function getExtension($path){
		
		if(empty($path)){
			return null;
		}
		
		if(!strpos($path, ".")){
			return null;
		}
		$array = explode(".", $path);
		
		if(!count($array)){
			return null;
		}
		
		return $array[count($array)-1];
	}

    /*
     * Create a file with the contents passed and save it to the path specified.
     * Uses the standard php putFileContents method. Can also create the folder
     * structure if required.
     * @method putFileContents
     * @param string $path path to file 
     * @param string $contents contents to be written
     * @param bool $overwrite should the file be overwritten if already created
     * @param bool $createFolders should the folder structure be created if it
     * doesn't exist
     * @return string file extension
     */
	public static function putFileContents($path, $contents, $overwrite=false, $createFolders = false){

		$path = self::normalisePath($path);
		// Get the folder path
		$path_folder = self::path_pop($path);

		if($createFolders){
			// Ensure the folder exists. Create it
			$created = self::createFoldersFromPath($path_folder);
			if(!$created){
				return false;
			}
		}

		if(self::exists($path)){
			self::deleteFile($path);
		}
		
		file_put_contents($path, $contents);
		return true;
	}
	
    /*
     * Delete a file on the file system. Link(self::unlink())
     * @method deleteFile
     * @param string $path path to file 
     * @return bool
     */
	public static function deleteFile($path){
		return self::unlink($path);
	}
	
    /*
     * Delete a file on the file system.
     * @method unlink
     * @param string $path path to file 
     * @return bool
     */
	public static function unlink($path){
		return unlink($path);
	}
	
    /*
     * Create a folder on the file system.
     * @method unlink
     * @param string $path path to file 
     * @return bool
     */
	public static function createFolder($path){
		
		if(is_dir($path)){
			return true;
		}
	
		return mkdir($path);
	}
	
	/*
	 * Delete a folder and all its contents including sub directories
	 * @method deleteFolder
     * @param string $path path to folder
	 * @return boolean indicates success or failure. 
	 */
	public static function deleteFolder($path){
		GLOBAL $Out;
	
		$success = true;
		if(!self::exists($path, "d")){
			return true;
		}
		
		// delete the contents of the folder
		$folder = self::openFolder($path);
		if($folder) {
		    while($object = readdir($folder)) {
		      if($object != '.' && $object != '..') {
		      	
		      	$objectPath =$path.'/'.$object;

		      	if(self::isFile($objectPath)){

		      		if(!self::deleteFile($objectPath)){
		      			$success=false;
		      		}
		      		continue;
		      	}
		      	
		      	if(self::isFolder($objectPath)){
		      		if(!self::deleteFolder($objectPath)){
		      			$success=false;
		      		}
		      		continue;
		      	}
		      	
		      }
		    }
		}
		self::closeFolder($folder);
		if(!rmdir($path)){
			$success=false;
		}
		return $success;
	}
	
    /*
     * Open a folder on the file system and returns a handle to it
     * @method openFolder
     * @param string $path path to folder
     * @return object folder handle
     */
	public static function openFolder($path){
		return opendir($path);
	}
	
    /*
     * Pass in a handle to a folder and close the handle
     * @method closeFolder
     * @param object folder handle
     * @return void 
     */
	public static function closeFolder($folder){
		closedir($folder);
	}
	
	function destructor(){

	}
	
    /*
     * Check whether a path refers to a folder
     * @method isFolder
     * @param string $path  
     * @return bool 
     */
	public static function isFolder($path){
		return is_dir($path);
	}
	
    /*
     * Check whether a path refers to a file
     * @method isFile
     * @param string $path 
     * @return bool 
     */
	public static function isFile($path){
		return is_file($path);
	}
	
    /*
     * A function to copy files from one directory to another one, including subdirectories and
     * nonexisting or newer files. Function returns number of files copied.
     * 
     * This function is a PHP implementation of Windows xcopy  A:\dir1\* B:\dir2
     * /D /E /F /H /R /Y 
     * 
     * Syntax:  [$number =] dircopy($sourcedirectory,$destinationdirectory
     * [,$verbose]);
     * 
     * Example: $num = dircopy('A:\dir1', 'B:\dir2', 1);
     * 
     * @method copyFolder
     * @param string $srcdir source directory to copy from 
     * @param string $dstdir destination directory to copy to
     * @param bool $recursive just copy one level or include sub folders
     * recursively
     * @return bool
     */
	public static function copyFolder($srcdir, $dstdir, $recursive = false) {
		
	  $num = 0; // Number of files copied
	  $errors = array(); // Array to record all errors that are generated.
      // TODO find the best way to return the error collection. Currently it is ignored and therefore irrelevant.
      // Perhaps package it into an object that include the num files and errors or pass the errors array in by 
      // reference so it is available outside the method 
       
	  
      // Does the destination directory alrady exist
	  if(!self::exists($dstdir, "d")) {
	    // it doesn't so create the necessary folder structure
        self::createFoldersFromPath($dstdir);
	  }
	  
	  if($curdir = opendir($srcdir)) {
	    // The folder was opened so begin reading its contents
        while($file = readdir($curdir)) {
	      if($file != '.' && $file != '..') {
	        // Create the full file paths
            $srcfile = $srcdir . '/' . $file;
	        $dstfile = $dstdir . '/' . $file;
	
	        if(self::isFile($srcfile)) {
	          if(self::isFile($dstfile)){ 
	          	/*
                 * If the destination file already exists record the time
                 * different
	          	 */
                $ow = filemtime($srcfile) - filemtime($dstfile);
	          }
	          else {
	          	$ow = 1;
	          }
	          
              /*
               * Only  copy the file if it does not exist in the destination
               * folder or is different from that already there.
               */
	          if($ow > 0) {
	            if(copy($srcfile, $dstfile)) {
	              touch($dstfile, filemtime($srcfile)); 
	              $num++;
	            }
	            else{ 
	            	$errors[] = "Error: File '$srcfile' could not be copied!";
	            }
	          }                  
	        }
	        else if($recursive && self::isFolder($srcfile)) {
	          $num += self::copyFolder($srcfile, $dstfile);
	        }
	      }
	    }
	    closedir($curdir);
	  }
	  return $num;
	}
	
    /*
     * Get the contents of a folder as an array of objects
     * @method getFolderContents
     * @param string $path path to folder
     * @return array array of file and folder objects 
     */
	public static function getFolderContents($path, $recursive = false) {
		
	  $contents = array();
	  
	  if(!self::exists($path, "d")) {
	    return null;
	  }
	  
	  if($curdir = opendir($path)) {
	    while($file = readdir($curdir)) {
	      $contents[] = $file;
	    }
	    closedir($curdir);
	  }
	  return $contents;
	}
    
    /* 
     * Appends a value to the end of a file name leaving the extension untouched
     * @method appendToFileName
     * @param string $name file name
     * @param string $value value to add
     * @param string $delimiter delimiter used to split the filename. Default is
     * '.'
     * @return string
     */
    public static public static function appendToFileName($name, $value, $delimiter='.'){
    	
        // split the file name using the delimiter so the extension is separated from the file name
        $strings = explode($delimiter, $name);

        if(!is_array($strings)){
            // no file extension so just append the value directly to the end
            return $name.$value;
        }

        $strings[count($strings)-2].=$value;
        return implode('.', $strings);
    }
    
    /* 
     * Retrieve a portion of a file name by splitting the name according to a
     * delimiter, usually '.' and returning the value at the given index
     * @method getFromFileName
     * @param string $name file name
     * @param int $index Works backwards from file extension
     * @param string $delimiter delimiter used to split the filename. Default is
     * '.'
     * @return string
     */
    public static public static function getFromFileName($name, $index=0, $delimiter='.'){
        
        // split the file name to get an array of indexed strings
        $strings = explode($delimiter, $name);

        // Counts backwards from the end of the array so $index must be greater than 0
        $index++;
        
        if(!is_array($strings) || !isset($strings[count($strings)-$index])){
            // name cannot be slit or the required index does not exist
            return null;
        }

        return $strings[count($strings)-$index];
    }
    
    /* 
     * @method removeFromFileName Removes a value from the file name leaving the
     * extension untouched
     * @return string
     */
     
     /* 
     * Removes a value from the file name leaving the
     * extension untouched
     * @method removeFromFileName
     * @param string $name file name
     * @param string $value value to remove
     * @param string $delimiter delimiter used to split the filename. Default is
     * '.'
     * @return string
     */
    public static public static function removeFromFileName($name, $value, $delimiter='.'){
        
        // split the file name to get an array of strings
        $strings = explode($delimiter, $name);

        if(!is_array($strings)){
            // the name cannot be split
            return $name;
        }

        // create a new array to store the new name
        $newString = array();
        
        /*
         * Loop through the strings array copying each string that doesn't match
         * the value that was passed in.
         */
        foreach($strings as $string){
        	if($string == $value){
        		continue;
        	}
            $newString[] = $string;
        }
        
        return implode('.', $newString);
    }
	
    
    /*
     * Serialise the item passed in and store it in a file.
     * @method restoreMergedBackup Restore the merged backup
     * @param mixed $object item to serialise
     * @param string $path path to save the file to
     * @return bool has the action succeeded
     */
    public static function serializeToFile($object, $path){
        
        if(!isset($object)){
            return false;
        }
        
        /*
         * TODO use the createFolder from path method to ensure the folder
         * structure is present.
         */
         
        FileSystem::putFileContents($path,serialize($object),true, true);
        
        return true;
    }
    
    /*
     * Retrieve the contents of a file from the path passed in. If there are
     * contents return them as an unserialised item 
     * @method unSerializeFromFile 
     * @param string $path path to get the file from
     * @return mixed unserialised item
     */
    public static function unSerializeFromFile($path){
        
        if(!isset($path)){
            return null;
        }
        
        $contents = FileSystem::getFileContents($path);
        
        if(!isset($contents)){
            return null;
        }
        
        $object = unserialize($contents);
        return $object;
    }
}
?>