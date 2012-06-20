<?php
	 require_once dirname(__FILE__). "/../synch-setup.php";
	require_once $CFG->dirroot.'/group/synch/import_all.php';
	require_once($CFG->dirroot.'/group/lib.php');

	GLOBAL $GroupBeanFactory, $GroupSynchManager;
	$GroupBeanFactory = new group_bean_Factory();
	$GroupSynchManager = new group_synch_manager();
	?>