<?php

/*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * This  file is used to transfer a file from the temporary download location
 * into the moodle synch queue.
 */
 
require_once dirname(__FILE__)."/setup.php";

 GLOBAL $CFG;
 
 // Get the path of the temporary file 
 $from = $_FILES['file1']['tmp_name'];
 $path = $CFG->synch->path_queue_in;
 
 // If the file is attached to a session save it in a session folder 
 if(isset($_POST['sessionId'])){
 	$path.='/'.$_POST['sessionId'];
 }
 
 // Create the path to move the file to
 $to = $path.'/'.$_FILES['file1']['name'];

 // Create folders if they aren't already there
 FileSystem::createFoldersFromPath($path);
 
 FileSystem::moveFile($from, $to);

?>