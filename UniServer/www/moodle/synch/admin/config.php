<?php
/*
 * Created on 2 Mar 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 GLOBAL $CFG;
 //$CFG->synch = new object;
 $CFG->synch->databases = new object;
 $CFG->synch->databases->master = new object;
 $CFG->synch->databases->master->host = $CFG->dbhost;
 $CFG->synch->databases->master->username = "off-dev-srv-19";
 $CFG->synch->databases->master->password = $CFG->dbpass;
 $CFG->synch->databases->master->name = "off-dev-server-19-mdl";
 $CFG->synch->databases->master->prefix = "mdl_";
 $CFG->synch->databases->master->instance = null;
 
 
 $CFG->synch->databases->client = new object;
 $CFG->synch->databases->client->host = $CFG->dbhost;
 $CFG->synch->databases->client->username = $CFG->dbuser;
 $CFG->synch->databases->client->password = $CFG->dbpass;
 $CFG->synch->databases->client->name = $CFG->dbname;
 $CFG->synch->databases->client->prefix = $CFG->prefix;
 $CFG->synch->databases->client->instance = null;
?>
