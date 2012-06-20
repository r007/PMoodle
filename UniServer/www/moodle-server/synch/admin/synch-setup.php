<?php

	require_once(dirname(__FILE__).'/../setup.php');
	GLOBAL $CFG;
    error_reporting(E_ALL);
    // Imports
	require_once($CFG->dirroot.'/synch/admin/config.php');
    GLOBAL $db, $CFG;
    $SynchManager->setDatabaseByReference('client', $db, $CFG->prefix);
    $master = synch_connect_to_database($CFG->synch->databases->master);
    $SynchManager->setDatabaseByReference('master', $master);