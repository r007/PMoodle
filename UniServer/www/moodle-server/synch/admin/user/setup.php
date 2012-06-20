<?php
	require_once dirname(__FILE__). "/../synch-setup.php";
	require_once $CFG->dirroot.'/user/synch/import_all.php';
	require_once($CFG->dirroot.'/user/editlib.php');


	GLOBAL $UserBeanFactory, $UserSynchManager;
	$UserBeanFactory = new user_bean_Factory();
	$UserSynchManager = new user_synch_manager();
	$UserSynchManager->initialise();
	
?>