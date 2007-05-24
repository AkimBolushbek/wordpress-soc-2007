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
}
add_action('init', 'wpupdate_init');

function wpupdate_admin_init(){
	//Override Plugins and Themes pages.
	add_action('load-plugins.php', 'wpupdate_plugins');
	add_action('load-themes.php', 'wpupdate_themes');
	//Add extra Subpages.
}
function wpupdate_plugins($arg = ''){
	global $wpdb,$menu,$submenu;
	include('wp-update-plugins.php');
}
function wpupdate_themes($arg = ''){
	global $wpdb,$menu,$submenu;
	include('wp-update-themes.php');
}

?>