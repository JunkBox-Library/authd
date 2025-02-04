﻿<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}


require_once($CFG->libdir.'/authlib.php');



class auth_plugin_jbxl extends auth_plugin_base {

    var $userfields = array(
        'lastname',
        'firstname',
        'email',
        'city',
        'country',
        'lang',
        'description',
        'url',
        'idnumber',
        'institution',
        'department',
        'phone1',
        'phone2',
        'address'
    );



    function auth_plugin_jbxl() {
        $this->authtype = 'jbxl';
        $this->config = get_config('auth/jbxl');
    }


    function user_login($username, $password) 
    {
        global $CFG;

        $host = $this->config->host;
        $port = $this->config->port;

        if (!function_exists('jbxl_check_auth')) {
            print_error('auth_jbxlnotinstalled', 'auth_jbxl');
            exit;
        }

        error_reporting(0);
        $ret = jbxl_check_auth($host, $port, $username, $password, 0);
        error_reporting($CFG->debug);

        if ($ret==1) {
            return true;
        } 
        else {
            return false;
        }
    }


    function user_exists($username) 
    {
        global $CFG;

        if (preg_match('/^[A-Za-z]\d\d\d\d\d$/', $username)) return true;
        if (preg_match('/^[A-Za-z]\d\d\d\d\d[A-Za-z][A-Za-z]$/', $username)) return true;

        $host = $this->config->host;
        $port = $this->config->port;

        if (!function_exists('jbxl_check_auth')) {
            return false;
        }

        error_reporting(0);
        $ret = jbxl_check_auth($host, $port, $username, "passwd", 0);
        error_reporting($CFG->debug);

        if ($ret==2 || $ret==1) {
            return true;
        } 
        else {
            return false;
        }
    }


    function get_userinfo($username) {
        $userinfo = array();

        if (preg_match('/^[a-z]\d\d\d\d\d[a-z][a-z]$/', $username)) {
        	$usermail = $username.'@edu.jbxl.jp';
            $username = substr($username, 0, 6);
        }
		else {
        	$usermail = $username.'@rsch.jbxl.jp';
		}

/*
        'lastname',
        'firstname',
        'email',
        'city',
        'country',
        'lang',
        'description',
        'url',
        'idnumber',
        'institution',
        'department',
        'phone1',
        'phone2',
        'address'
*/
        $userinfo['lastname'] = $username;
        $userinfo['firstname'] = $username;
        $userinfo['email'] = $usermail;
        $userinfo['city'] = 'Chiba';
        $userinfo['country'] = 'JP';

        return $userinfo;
    }



    function prevent_local_passwords() {
        return true;
    }


    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return false;
    }


    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return false;
    }


    /**
     * Returns the URL for changing the user's pw, or false if the default can
     * be used.
     *
     * @return bool
     */
    function change_password_url() {
        return $this->config->changepasswordurl;
    }


    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        include "config.html";
    }


    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        // set to defaults if undefined
        if (!isset ($config->host)) {
            $config->host = '127.0.0.1';
        }
        if (!isset ($config->port)) {
            $config->port = '9000';
        }
        if (!isset($config->changepasswordurl)) {
            $config->changepasswordurl = '';
        }

        // save settings
        set_config('host', $config->host, 'auth/jbxl');
        set_config('port', $config->port, 'auth/jbxl');
        set_config('changepasswordurl', $config->changepasswordurl, 'auth/jbxl');

        return true;
    }


    function get_title() {
        return get_string("auth_jbxlauthtitle", "auth_jbxl");
    }


    function get_description() {
        return get_string("auth_jbxlauthdescription", "auth_jbxl");
    }
}

?>
