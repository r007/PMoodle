<?php
	require_once dirname(__FILE__). "/../synch-setup.php";
	require_once $CFG->dirroot.'/admin/roles/synch/import_all.php';

	GLOBAL $RoleBeanFactory, $RoleSynchManager;
	$RoleBeanFactory = new role_bean_Factory();
	$RoleSynchManager = new role_synch_manager();
	?>