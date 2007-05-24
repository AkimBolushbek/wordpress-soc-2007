<?php
require_once('admin.php');
require_once('wp-update-class.php');
global $wpupdate;
$wpupdate = new WP_Update;

if ( isset($_GET['action']) ) {
	if ('activate' == $_GET['action']) {
		check_admin_referer('activate-plugin_' . $_GET['plugin']);
		$current = get_option('active_plugins');
		$plugin = trim($_GET['plugin']);
		if ( validate_file($plugin) )
			wp_die(__('Invalid plugin.'));
		if ( ! file_exists(ABSPATH . PLUGINDIR . '/' . $plugin) )
			wp_die(__('Plugin file does not exist.'));
		if (!in_array($plugin, $current)) {
			wp_redirect('plugins.php?error=true'); // we'll override this later if the plugin can be included without fatal error
			@include(ABSPATH . PLUGINDIR . '/' . $plugin);
			$current[] = $plugin;
			sort($current);
			update_option('active_plugins', $current);
			do_action('activate_' . $plugin);
		}
		wp_redirect('plugins.php?activate=true'); // overrides the ?error=true one above
	} else if ('deactivate' == $_GET['action']) {
		check_admin_referer('deactivate-plugin_' . $_GET['plugin']);
		$current = get_option('active_plugins');
		array_splice($current, array_search( $_GET['plugin'], $current), 1 ); // Array-fu!
		update_option('active_plugins', $current);
		do_action('deactivate_' . trim( $_GET['plugin'] ));
		wp_redirect('plugins.php?deactivate=true');
	}
	exit;
}
//Having this here causes a bug.... it matches itself.
function wpupdate_get_plugin_data( $plugin_file ) {
	$plugin_data = implode( '', file( $plugin_file ));
	preg_match( "|Plugin Name:(.*)|i", $plugin_data, $plugin_name );
	preg_match( "|Plugin URI:(.*)|i", $plugin_data, $plugin_uri );
	preg_match( "|Description:(.*)|i", $plugin_data, $description );
	preg_match( "|Author:(.*)|i", $plugin_data, $author_name );
	preg_match( "|Author URI:(.*)|i", $plugin_data, $author_uri );
	if ( preg_match( "|Version:(.*)|i", $plugin_data, $version ))
		$version = trim( $version[1] );
	else
		$version = '';

	$description = wptexturize( trim( $description[1] ));

	$name = $plugin_name[1];
	$name = trim( $name );
	$plugin = $name;
	if ('' != $plugin_uri[1] && '' != $name ) {
		$plugin = '<a href="' . trim( $plugin_uri[1] ) . '" title="'.__( 'Visit plugin homepage' ).'">'.$plugin.'</a>';
	}

	if ('' == $author_uri[1] ) {
		$author = trim( $author_name[1] );
	} else {
		$author = '<a href="' . trim( $author_uri[1] ) . '" title="'.__( 'Visit author homepage' ).'">' . trim( $author_name[1] ) . '</a>';
	}

	return array ('Name' => $name, 'Title' => $plugin, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Template' => $template[1] );
}

function wpupdate_get_plugins() {
	global $wp_plugins;

	if ( isset( $wp_plugins ) ) {
		return $wp_plugins;
	}

	$wp_plugins = array ();
	$plugin_root = ABSPATH . PLUGINDIR;

	// Files in wp-content/plugins directory
	$plugins_dir = @ dir( $plugin_root);
	if ( $plugins_dir ) {
		while (($file = $plugins_dir->read() ) !== false ) {
			if ( preg_match( '|^\.+$|', $file ))
				continue;
			if ( is_dir( $plugin_root.'/'.$file ) ) {
				$plugins_subdir = @ dir( $plugin_root.'/'.$file );
				if ( $plugins_subdir ) {
					while (($subfile = $plugins_subdir->read() ) !== false ) {
						if ( preg_match( '|^\.+$|', $subfile ))
							continue;
						if ( preg_match( '|\.php$|', $subfile ))
							$plugin_files[] = "$file/$subfile";
					}
				}
			} else {
				if ( preg_match( '|\.php$|', $file ))
					$plugin_files[] = $file;
			}
		}
	}

	if ( !$plugins_dir || !$plugin_files )
		return $wp_plugins;

	foreach ( $plugin_files as $plugin_file ) {
		if ( !is_readable( "$plugin_root/$plugin_file" ) )
			continue;

		$plugin_data = wpupdate_get_plugin_data( "$plugin_root/$plugin_file" );

		if ( empty ( $plugin_data['Name'] ) )
			continue;

		$wp_plugins[plugin_basename( $plugin_file )] = $plugin_data;
	}

	uasort( $wp_plugins, create_function( '$a, $b', 'return strnatcasecmp( $a["Name"], $b["Name"] );' ));

	return $wp_plugins;
}

$title = __('Manage Plugins++');
$parent_file = 'plugins.php';

require_once('admin-header.php');

// Clean up options
// If any plugins don't exist, axe 'em

$check_plugins = get_option('active_plugins');

// Sanity check.  If the active plugin list is not an array, make it an
// empty array.
if ( !is_array($check_plugins) ) {
	$check_plugins = array();
	update_option('active_plugins', $check_plugins);
}

// If a plugin file does not exist, remove it from the list of active
// plugins.
foreach ($check_plugins as $check_plugin) {
	if (!file_exists(ABSPATH . PLUGINDIR . '/' . $check_plugin)) {
			$current = get_option('active_plugins');
			$key = array_search($check_plugin, $current);
			if ( false !== $key && NULL !== $key ) {
				unset($current[$key]);
				update_option('active_plugins', $current);
			}
	}
}
?>

<?php if ( isset($_GET['error']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('Plugin could not be activated because it triggered a <strong>fatal error</strong>.') ?></p></div>
<?php elseif ( isset($_GET['activate']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('Plugin <strong>activated</strong>.') ?></p></div>
<?php elseif ( isset($_GET['deactivate']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('Plugin <strong>deactivated</strong>.') ?></p></div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Plugin Management'); ?></h2>
<p><?php _e('Plugins extend and expand the functionality of WordPress. Once a plugin is installed, you may activate it or deactivate it here.'); ?></p>
<?php

if ( get_option('active_plugins') )
	$current_plugins = get_option('active_plugins');

$plugins = get_plugins();
var_dump($plugins);

if (empty($plugins)) {
	echo '<p>';
	_e("Couldn&#8217;t open plugins directory or there are no plugins available."); // TODO: make more helpful
	echo '</p>';
} else {
?>
<table class="widefat plugins">
	<thead>
	<tr>
		<th><?php _e('Plugin'); ?></th>
		<th style="text-align: center"><?php _e('Version'); ?></th>
		<th style="text-align: center"><?php _e('Update Status'); ?></th>
		<th><?php _e('Description'); ?></th>
		<th style="text-align: center"<?php if ( current_user_can('edit_plugins') ) echo ' colspan="2"'; ?>><?php _e('Action'); ?></th>
	</tr>
	</thead>
<?php
	$style = '';

	foreach($plugins as $plugin_file => $plugin_data) {
		$style = ('class="alternate"' == $style|| 'class="alternate active"' == $style) ? '' : 'alternate';

		if (!empty($current_plugins) && in_array($plugin_file, $current_plugins)) {
			$toggle = "<a href='" . wp_nonce_url("plugins.php?action=deactivate&amp;plugin=$plugin_file", 'deactivate-plugin_' . $plugin_file) . "' title='".__('Deactivate this plugin')."' class='delete'>".__('Deactivate')."</a>";
			$plugin_data['Title'] = "<strong>{$plugin_data['Title']}</strong>";
			$style .= $style == 'alternate' ? ' active' : 'active';
		} else {
			$toggle = "<a href='" . wp_nonce_url("plugins.php?action=activate&amp;plugin=$plugin_file", 'activate-plugin_' . $plugin_file) . "' title='".__('Activate this plugin')."' class='edit'>".__('Activate')."</a>";
		}

		$plugins_allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());

		// Sanitize all displayed data
		$plugin_data['Title']       = wp_kses($plugin_data['Title'], $plugins_allowedtags);
		$plugin_data['Version']     = wp_kses($plugin_data['Version'], $plugins_allowedtags);
		$plugin_data['Description'] = wp_kses($plugin_data['Description'], $plugins_allowedtags);
		$plugin_data['Author']      = wp_kses($plugin_data['Author'], $plugins_allowedtags);

		if ( $style != '' )
			$style = 'class="' . $style . '"';
		if ( is_writable(ABSPATH . PLUGINDIR . '/' . $plugin_file) )
			$edit = "<a href='plugin-editor.php?file=$plugin_file' title='".__('Open this file in the Plugin Editor')."' class='edit'>".__('Edit')."</a>";
		else
			$edit = '';
		
		echo "
	<tr $style>
		<td class='name'>{$plugin_data['Title']}</td>
		<td class='vers'>{$plugin_data['Version']}</td>
		<td class='vers'>Update Status Here{$plugin_data['Update']}</td>
		<td class='desc'><p>{$plugin_data['Description']} <cite>".sprintf(__('By %s'), $plugin_data['Author']).".</cite></p></td>
		<td class='togl'>$toggle</td>";
		if ( current_user_can('edit_plugins') )
		echo "
		<td>$edit</td>";
		echo"
	</tr>";
	}
?>

</table>
<?php
}
?>

<p><?php printf(__('If something goes wrong with a plugin and you can&#8217;t use WordPress, delete or rename that file in the <code>%s</code> directory and it will be automatically deactivated.'), PLUGINDIR); ?></p>

<h2><?php _e('Get More Plugins'); ?></h2>
<p><?php _e('You can find additional plugins for your site in the <a href="http://wordpress.org/extend/plugins/">WordPress plugin directory</a>.'); ?></p>
<p><?php printf(__('To install a plugin you generally just need to upload the plugin file into your <code>%s</code> directory. Once a plugin is uploaded, you may activate it here.'), PLUGINDIR); ?></p>

</div>

<?php
include('admin-footer.php');
exit; //Prevent normal plugin page
?>
