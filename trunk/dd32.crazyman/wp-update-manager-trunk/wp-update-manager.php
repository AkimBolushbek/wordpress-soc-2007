<?php
/*
Plugin Name: wp-Update-Manager
Plugin URI: http://dd32.id.au/
Description: A Plugin to manage your created Plugins and Themes.
Version: 0.1
Author: Dion Hulse
Author URI: http://dd32.id.au/
*/

add_action('init', 'wpupdatemanager_init');
function wpupdatemanager_init() {
	
}

add_action('template_redirect','wpupdatemanager_template');
function wpupdatemanager_template($arg){
	global $wp_query,$wp_rewrite;
	if( !isset($wp_query->query_vars['pluginupdate']) )
		return $arg;

	var_dump($wp_query->query_vars);
	var_dump($wp_rewrite->rules);
	die();
}

add_action('admin_menu', 'wpupdatemanager_admin_init');
function wpupdatemanager_admin_init(){
	global $pagenow;
	
	add_options_page('Wp-Update-Manager','Wp-Update Manager','administrator','wp-update-manager/wp-update-manager-options.php');
	wp_enqueue_script('interface'); //jQuery
}

add_action('generate_rewrite_rules', 'wpupdatemanager_add_rewrite_rules');
function wpupdatemanager_add_rewrite_rules( $wp_rewrite ) {
	$new_rules = array( 'pluginupdate/(.+)' => 'index.php?pluginupdate=' . $wp_rewrite->preg_index(1) );
	
	$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}

add_filter('query_vars', 'wpupdatemanager_queryvars' );
function wpupdatemanager_queryvars( $qvars ){
	$qvars[] = 'pluginupdate';
	return $qvars;
}

register_activation_hook(__FILE__,'wpupdatemanager_activate');
function wpupdatemanager_activate(){
	//Flush the rewrite rules so that the new rules from this plugin get added.
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}


?>