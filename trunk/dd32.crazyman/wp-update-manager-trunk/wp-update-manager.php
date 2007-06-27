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
	add_action('admin_menu', 'wpupdatemanager_admin_init');
	
	//update_option('rewrite_rules', '');
	//add_filter('rewrite_rules_array','wpupdatemanager_createRewriteRules');
	//wpupdatemanager_createRewriteRules();
}

function wpupdatemanager_admin_init(){
	global $pagenow;
	
	add_options_page('Wp-Update-Manager','Wp-Update Manager','administrator','wp-update-manager/wp-update-manager-options.php');
	wp_enqueue_script('interface'); //jQuery
}

function wpupdatemanager_createRewriteRules($rewrite='') {
	if($rewrite=='') $rewrite = array();
	global $wp_rewrite;
	$wp_rewrite->rules[] = "DD32";
	
	add_rewrite_endpoint('pluginupdate/(.+)/?$','/wp-content/plugins/wp-update-manager/wp-update-manager-ajax.php');
	
	var_dump($wp_rewrite->endpoints);
	return;
	// add rewrite tokens
	$keytag_token = '%pluginupdate%';
	$wp_rewrite->add_rewrite_tag($keytag_token, '(.+)', 'updateplugin=');
	
	$keywords_structure = $wp_rewrite->root . "/pluginupdate/$keytag_token";
	$keywords_rewrite = $wp_rewrite->generate_rewrite_rules($keywords_structure);

	var_dump(array_merge($rewrite , $keywords_rewrite));
	return ( array_merge($rewrite , $keywords_rewrite) );
}

function wpupdatemanager_test(){
`net send dd32 test`;
}



?>