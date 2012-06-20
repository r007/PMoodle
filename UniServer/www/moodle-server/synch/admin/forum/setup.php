<?php
	require_once dirname(__FILE__). "/../synch-setup.php";
	require_once $CFG->dirroot.'/mod/forum/synch/import_all.php';
	require_once($CFG->dirroot.'/mod/forum/lib.php');

	GLOBAL $ForumBeanFactory, $ForumSynchManager;
	$ForumBeanFactory= new forum_bean_Factory();

	$SynchManager = new synch_Synch_controller();
	$ForumSynchManager = new forum_synch_manager();

	require_once dirname(__FILE__). "/../course/setup.php";
	?>