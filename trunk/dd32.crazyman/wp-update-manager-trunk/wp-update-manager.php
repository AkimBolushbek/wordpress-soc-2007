<?php
/*
Plugin Name: wp-Update-Manager
Plugin URI: http://dd32.id.au/
Description: A Plugin to manage your created Plugins and Themes.
Version: 0.1
Author: Dion Hulse
Author URI: http://dd32.id.au/
Update URI: http://dd32.no-ip.com:8080/wordpress/pluginupdate/wp-update-manager/
*/

add_action('template_redirect','wpupdatemanager_template');
function wpupdatemanager_template($arg){
	global $wp_query;
	if( !isset($wp_query->query_vars['pluginupdate']) )
		return $arg;
	
	/* If we've reached this point, then it means we've hit a plugin update request */
	$items = get_option('wpum_items');
	$itemId = false;
	foreach($items as $id=>$item){
		/* This needs to compare Slugs rather than name. */
		if( $item['slug'] == strtolower($wp_query->query_vars['pluginupdate']) ){
			/* We've found an item, Record it and break */
			$itemId = $id;
			break;
		}
	}
	if( $itemId === false ){
		/* Plugin not found: array('Errors'=> array('Unknown Plugin') )*/
		die('a:1:{s:6:"Errors";a:1:{i:0;s:14:"Unknown Plugin";}}');
	}
	
	$requirements = array();
	foreach((array)$items[$itemId]['requirements'] as $id=>$req)
		$requirements[] = array(
								'Type' => $req['type'],
								'Name' => $req['name'],
								'Min' => $req['min'],
								'Tested' => $req['tested']
								);
	
	$itemDetails = array(
				'Name' 		=>	$items[$itemId]['name'],
				'Version'	=>	$items[$itemId]['version'],
				'LastUpdate'=>	$items[$itemId]['lastupdated'],
				'Download'	=>	$items[$itemId]['download'],
				'Author'	=>	$items[$itemId]['author'],
				'AuthorHome'=>	$items[$itemId]['authorhome'],
				'PluginHome'=>	$items[$itemId]['pluginhome'],
				/*'Rating'	=>	$items[$itemId]['rating'],*/
				'Tags'		=>	array(),
				'Related'	=>	array(),
				'Requirements' => $requirements,
				'Expire'	=> 7*24*60*60
				);

	echo serialize($itemDetails);
	/*var_dump($itemDetails);
	var_dump($items[$itemId]);
	var_dump($items);*/
	die();
}

//Add the administrative menus, only 'Administrators' can access it for now.
add_action('admin_menu', 'wpupdatemanager_admin_init');
function wpupdatemanager_admin_init(){
	global $pagenow;
	
	add_options_page('Wp-Update-Manager','Wp-Update Manager','administrator','wp-update-manager/wp-update-manager-options.php');
	if( 'options-general.php' == $pagenow && isset( $_GET['page'] ) &&
		'wp-update-manager/wp-update-manager-options.php' == $_GET['page'])
		wp_enqueue_script('interface'); //jQuery
}

//If the rewrite rules are regenerated, Add our pretty permalink stuff, redirect it to the correct queryvar
add_action('generate_rewrite_rules', 'wpupdatemanager_add_rewrite_rules');
function wpupdatemanager_add_rewrite_rules( $wp_rewrite ) {
	$new_rules = array( 'pluginupdate/(.+)' => 'index.php?pluginupdate=' . $wp_rewrite->preg_index(1) );
	
	$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}

//Add a Query Var, This allows us to access the query var via $wp_query
add_filter('query_vars', 'wpupdatemanager_queryvars' );
function wpupdatemanager_queryvars( $qvars ){
	$qvars[] = 'pluginupdate';
	return $qvars;
}

//Add a Activation hook for the current page:
// NOTE: Broken in WP21/22 on win32, see http://trac.wordpress.org/ticket/3002
register_activation_hook(__FILE__,'wpupdatemanager_activate');
function wpupdatemanager_activate(){
	//Flush the rewrite rules so that the new rules from this plugin get added.
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}


?>