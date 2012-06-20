<?php
	require_once dirname(__FILE__). "/../synch-setup.php";
	require_once $CFG->dirroot.'/course/synch/import_all.php';
	require_once($CFG->dirroot.'/course/lib.php');

	GLOBAL $CourseBeanFactory, $CourseSynchManager;
	$CourseBeanFactory = new course_bean_Factory();
	$CourseSynchManager = new course_synch_manager();
	
	require_once dirname(__FILE__). "/../block/setup.php";
	?>