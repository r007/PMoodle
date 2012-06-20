<?php

/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: Moodle Network Authentication
 *
 * Multiple host authentication support for Moodle Network.
 *
 * 2006-11-01  File created.
 */

/**
 * Moodle Network authentication plugin.
 */
class auth_plugin_mnet {

    /**
     * Constructor.
     */
    function auth_plugin_mnet() {
        $this->authtype = 'mnet';
        $this->config = get_config('auth/mnet');
    }

    /**
     * Provides the allowed RPC services from this class as an array.
     * @return array  Allowed RPC services.
     */
    function mnet_publishes() {

        $sso_idp = array();
        $sso_idp['name']        = 'sso_idp'; // Name & Description go in lang file
        $sso_idp['apiversion']  = 1;
        $sso_idp['methods']     = array( 'fetch_user_image');


        return array($sso_idp);
    }

    /**
     * This function is normally used to determine if the username and password
     * are correct for local logins. Always returns false, as local users do not
     * need to login over mnet xmlrpc.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {
        return false; // error("Remote MNET users cannot login locally.");
    }

	
    /**
     * Returns the user's image as a base64 encoded string.
     *
     * @param int $userid The id of the user
     * @return string     The encoded image
     */
    function fetch_user_image() {
        global $CFG;
		//echo __FILE__.' fetch_user_image: called<br />';
        return "worked";
    }

}
?>
