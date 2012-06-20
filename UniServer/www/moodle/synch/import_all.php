<?php

/**
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * This file is meant to make it easier to include the entire synchroinsation library if necessary
 */
 
    //require_once dirname(__FILE__).'/lib/Out.php';  // Load the debugging class
	require_once dirname(__FILE__). '/lib.php';
    
    /*
     * Load the controller files
     */
    require_once dirname(__FILE__). '/controller/synch_synch_controller.php';
    require_once dirname(__FILE__). '/controller/synch_configuration_controller.php';
	require_once dirname(__FILE__). '/controller/synch_view_controller.php';
    require_once dirname(__FILE__). '/controller/synch_server_controller.php';
    require_once dirname(__FILE__). '/controller/synch_session_controller.php';
    require_once dirname(__FILE__). '/controller/synch_backup_controller.php';
    
    /*
     * Load the modal (Data storage) files
     */
    require_once($CFG->dirroot.'/synch/modal/BaseClass.class.php');
    require_once($CFG->dirroot.'/synch/modal/synch_data.class.php');
    require_once($CFG->dirroot.'/synch/modal/Modal.php');
    require_once($CFG->dirroot.'/synch/modal/ContentItem.php');
    require_once($CFG->dirroot.'/synch/modal/ContentHierarchy.php');
    require_once($CFG->dirroot.'/synch/modal/SessionItem.php');
    require_once($CFG->dirroot.'/synch/modal/Server.php');
    require_once($CFG->dirroot.'/synch/modal/Session.php');
    require_once($CFG->dirroot.'/synch/modal/SynchTypeConfiguration.php');
?>