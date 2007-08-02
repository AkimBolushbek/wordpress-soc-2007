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
	//Spit out the XML for it.
	wpupdatemanager_generate_xml($items[$itemId]);
	//Debug purposes, Display friendly formatted array
	if( isset($_GET['debug']) ){
		echo "<pre>";
		var_dump($items[$itemId]);
		echo "</pre>";
	}
	die();
}

function wpupdatemanager_generate_xml($items=false){
	echo '<?xml version="1.0" encoding="iso-8859-1"?>', "\n";
	if( ! $items || empty($items) )
		die('<error>Item not Found</error>'); //End output of the routine at this point
	if( isset($items['name']) )//Is this a multi dimension array, or just a single array of a plugin?
		$items = array($items); //set to a multi dimension

	$validTypes = array('plugin','theme');
	foreach($items as $plugin){
		if( 'theme' != $plugin['type'] )
			$plugin['type'] = 'plugin'; //Not a theme means it _must_ be a plugin
			
		echo "<{$plugin['type']}>\n";
			//echo accepts multiple arguements, no need to contact items together with .
			echo "\t<Name>"       ,	htmlentities($plugin['name']) 		, "</Name>\n";
			echo "\t<Version>"    ,	htmlentities($plugin['version']) 	, "</Version>\n";
			echo "\t<LastUpdate>" , htmlentities($plugin['lastupdated']), "</LastUpdate>\n";
			echo "\t<Download>"   ,	htmlentities($plugin['download']) 	, "</Download>\n";
			echo "\t<Author>" 	  ,	htmlentities($plugin['author']) 	, "</Author>\n";
			echo "\t<AuthorHome>" , htmlentities($plugin['authorhome']) , "</AuthorHome>\n";
			echo "\t<PluginHome>" , htmlentities($plugin['pluginhome']) , "</PluginHome>\n";
			
			echo "\t<Expire>". ( isset($plugin['expire']) ? $plugin['expire'] : 7*24*60*60 ) . "</Expire>\n"; //Default to recheck in a week

			if( count($plugin['requirements']) > 0 ){
				echo "\t<Requirements>\n";
				foreach($plugin['requirements'] as $req){
					if( empty($req['type']) )
						continue; //If the Requirement type is not set, then we ignore it.
					echo "\t\t<Requirement>\n";
						if( !empty($req['type']) )
							echo "\t\t\t<Type>{$req['type']}</Type>\n";
						if( !empty($req['name']) )
							echo "\t\t\t<Name>{$req['name']}</Name>\n";
						if( !empty($req['min']) )
							echo "\t\t\t<Min>{$req['min']}</Min>\n";
						if( !empty($req['tested']) )
							echo "\t\t\t<Tested>{$req['tested']}</Tested>\n";
					echo "\t\t</Requirement>\n";
				}
				echo "\t</Requirements>\n";
			}// end if count( requirements)
		echo "</{$plugin['type']}>\n";
	} //end foreach(items)
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