<?php
/*
Plugin Name: wp-Update
Plugin URI: http://dd32.id.au/
Description: A Plugin to keep WordPress Plugin and Themes up to date.
Version: 0.1
Author: Dion Hulse
Author URI: http://dd32.id.au/
*/
function wpupdate_init() {
	add_action('admin_menu', 'wpupdate_admin_init');
	/* global $wpcom_api_key, $akismet_api_host, $akismet_api_port;

	if ( $wpcom_api_key ) {
		$akismet_api_host = $wpcom_api_key . '.rest.akismet.com';
	} else {
		$akismet_api_host = get_option('wordpress_api_key') . '.rest.akismet.com';
	}

	$akismet_api_port = 80;
	add_action('admin_menu', 'akismet_config_page'); */
}
add_action('init', 'wpupdate_init');

function wpupdate_admin_init(){
	//add_submenu_page('plugins.php','Plugins','Plugins','edit_plugins','wp-update/wp-update-plugins.php');
	add_action('load-plugins.php', 'wpupdate_plugins');
}
function wpupdate_plugins($arg = ''){
	global $wpdb,$menu,$submenu;
	include('wp-update-plugins.php');
}

?>