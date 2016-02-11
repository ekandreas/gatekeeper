<?php
/*
Plugin Name:        Gatekeeper
Description:        Reuse of authentication functions
Version:            0.1
Author:             Andreas Ek
Author URI:         http://www.aekab.se/
License:            MIT License
License URI:        http://opensource.org/licenses/MIT
*/
if( !class_exists('Gatekeeper\Forgot')) {
	require_once('vendor/autoload.php');
}

include_once 'globals.php';
include_once 'filters.php';

