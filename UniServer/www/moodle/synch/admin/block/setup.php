<?php
	require_once dirname(__FILE__). "/../synch-setup.php";
	require_once $CFG->dirroot.'/blocks/synch/import_all.php';

	GLOBAL $BlockBeanFactory, $BlockSynchManager;
	$BlockBeanFactory = new block_bean_Factory();
	$BlockSynchManager = new block_synch_manager();
	?>