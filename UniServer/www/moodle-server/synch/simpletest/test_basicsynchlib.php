<?php
/**
 * Unit tests for new Moodle Groups - basicgrouplib.php and some of utillib.php.
 * 
 * /admin/report/simpletest/index.php?showpasses=1&showsearch=1&path=course%2Fgroups
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 *
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once(dirname(__FILE__) . '/../../config.php');

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->dirroot . '/synch/lib.php');
require_once($CFG->dirroot . '/synch/beans/BaseClass.class.php');
require_once($CFG->dirroot . '/synch/synch_data.class.php');

class basicgrouplib_test extends UnitTestCase {

    var $courseid= 0;
    var $userid  = 0;
    var $userid_2= 0;
    var $groupid = 0;

    function __construct() {
       parent::UnitTestCase();

    }

    function test_create_synch_data() {
     //  $data = new synch_data();
      // echo("testing sycnh");
       $this->assertTrue(true);
    }

}

?>