<?php

add_action('wp_ajax_nopriv_login', 'Gatekeeper\Login::ajax');
add_action('wp_ajax_nopriv_forgot', 'Gatekeeper\Forgot::ajax');
add_action('wp_ajax_nopriv_forgot_request', 'Gatekeeper\Forgot::request');
add_action('wp_ajax_nopriv_register', 'Gatekeeper\Register::ajax');

add_action('plugins_loaded', function() {
	load_plugin_textdomain( 'gatekeeper', false, dirname( plugin_basename(__FILE__) ) . '/lang' );
});

