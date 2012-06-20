<?php
 /*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * File description to go here
 */
 
 require_once('../../../config.php');
 require_once($CFG->dirroot.'/synch/setup.php');
 global $Out;
 
 $date = date('Ymd H : i');
 $Out->append('$date = '.$date);
 
 $elements = new object();
 $elements->name = 'SRV '.date('Ymd H : i');
 $Out->print_r($elements->name, '$data (1) = ');
 

 $sessionId = null;
 $id = '12';
 $type = synch_view_controller::$TYPE_ID_COURSE;
 
 /*
     * Begin the process of restoring the session
     * 
     * Load the session using the session id. If no session exists, create one.
     */
    $session = synch_Session_controller::loadSession($sessionId);
 
 $Out->append('$type = '.$type);
 $Out->flush();
 $created = $SynchManager->createBackupByIdAndType($session, $id, $type);
 
 print_box('coursenotcreated');
?>
