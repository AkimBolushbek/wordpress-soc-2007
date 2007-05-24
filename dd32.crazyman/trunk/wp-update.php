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
	global $pagenow;
	//Override Plugins and Themes pages.
	add_action('load-plugins.php', 'wpupdate_plugins', 9);
	add_action('load-themes.php', 'wpupdate_themes', 9);
	//Add extra Subpages. ; Perhaps hard-code them..?
	if( get_option('update_install_enable') ){
		add_submenu_page('plugins.php','Plugin Install','Plugin Install','edit_plugins','wp-update/wp-update-plugins-install.php');
		add_submenu_page('themes.php','Theme Install','Theme Install','edit_themes','wp-update/wp-update-themes-install.php');
	}
	if( get_option('update_plugin_search_enable') )
		add_submenu_page('plugins.php','Plugin Search','Plugin Search','edit_plugins','wp-update/wp-update-plugins-search.php');
	if( get_option('update_theme_search_enable') )
		add_submenu_page('themes.php','Theme Search','Theme Search','edit_themes','wp-update/wp-update-themes-search.php');
	
	
	add_options_page('Wp-Update','Wp-Update',8,'wp-update/wp-update-options.php');
	
	//Enqueue jQuery if we're on a page we're modifying
	if(	'themes.php' == $pagenow || 
		'plugins.php' == $pagenow)
		wp_enqueue_script('interface'); //jQuery
}
function wpupdate_plugins($arg = ''){
	global $wpdb,$menu,$submenu;
	include('wp-update-plugins.php');
	exit;
}
function wpupdate_themes($arg = ''){
	global $wpdb,$menu,$submenu;
	include('wp-update-themes.php');
	exit;
}

?>