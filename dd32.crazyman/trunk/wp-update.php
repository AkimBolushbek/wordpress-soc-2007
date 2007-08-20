<?php
/*
Plugin Name: wp-Update
Plugin URI: http://dd32.id.au/
Description: A Plugin to keep WordPress Plugin and Themes up to date.
Version: 0.1
Author: Dion Hulse
Author URI: http://dd32.id.au/
Update URI: http://dd32.id.au/pluginupdate/wp-update/

WordPress Update (wp-Update for short) is a plugin which allows you to view any plugin updates which are available for your currently installed plugins.
It also has the ability to install Themes and Plugins via the WordPress Administration Console as well as upgrading current plugins.

*/

add_action('admin_menu', 'wpupdate_admin_init');
function wpupdate_admin_init(){
	global $pagenow;
	//Override Plugins and Themes pages.
	add_action('load-plugins.php', 'wpupdate_plugins');
	add_action('load-themes.php', 'wpupdate_themes');
	
	//Add extra Subpages.
	if( get_option('update_install_enable') ){
		add_submenu_page('plugins.php','Plugin Install','Plugin Install','edit_plugins','wp-update/wp-update-plugins-install.php');
		add_submenu_page('themes.php','Theme Install','Theme Install','edit_themes','wp-update/wp-update-themes-install.php');
	}
	if( get_option('update_plugin_search_enable') )
		add_submenu_page('plugins.php','Plugin Search','Plugin Search','edit_plugins','wp-update/wp-update-plugins-search.php');

	if( get_option('update_theme_search_enable') )
		add_submenu_page('themes.php','Theme Search','Theme Search','edit_themes','wp-update/wp-update-themes-search.php');


	add_options_page('Wp-Update','Wp-Update','administrator','wp-update/wp-update-options.php');

	//Enqueue jQuery if we're on a page we're modifying
	if(	!isset($_GET['page']) && ('themes.php' == $pagenow || 'plugins.php' == $pagenow) )
		wpupdate_head();

	//Plugin pages
	add_action('admin_print_scripts-wp-update/wp-update-plugins-install.php','wpupdate_head');
	add_action('admin_print_scripts-wp-update/wp-update-plugins-search.php','wpupdate_head');

	//Theme pages
	add_action('admin_print_scripts-wp-update/wp-update-themes-install.php','wpupdate_head');
	add_action('admin_print_scripts-wp-update/wp-update-themes-search.php','wpupdate_head');

	//Option pages
	add_action('admin_print_scripts-wp-update/wp-update-options.php','wpupdate_head');
	
	//Notices (If the user is allowed to touch the plugins..)
	if ( current_user_can('edit_plugins') ) 
		add_action('admin_notices','wpupdate_notices');
}
function wpupdate_head(){
	wp_enqueue_script('jquery');
	wp_deregister_script('prototype'); //Deregister the prototype script so that it cant be used while on a wpupdate page.
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

function wpupdate_notices(){
	$updates = get_option('wpupdate_notifications');
	if( ! $updates )
		return;
	foreach((array)$updates as $plugin_file => $plugin_info){
		if( true == $plugin_info['HideUpdate'] )
			continue;
		$plugins[] = $plugin_info['PluginInfo']['Name'] . ' ' . $plugin_info['PluginInfo']['Version'];
	}
	if( empty($plugins) )
		return;
	echo '<div class="updated"><p>';
		printf(__('You have %d update(s) available.'),count($plugins));
		echo '&nbsp;<a href="plugins.php">' . __('Plugin page &raquo;') . '</a>';
		echo '<br /><strong>';
			echo implode(', ',$plugins);
		echo '</strong>';
		echo '<span style="float:right;"><a href="' . wp_nonce_url('plugins.php?action=hidenotifications', 'wpupdate-hide-notice') .'">' . __('Hide these updates') . '</a></span>';
	echo '</p></div>';
	return;
}

?>