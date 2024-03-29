<?php
require_once('admin.php');
require_once('includes/wp-update-class.php');
require_once('includes/wp-update-functions.php');
global $wp_update;
if( ! $wp_update || ! is_object($wp_update) )
	$wp_update = new WP_Update;

if ( isset($_GET['action']) ) {
	if ('hidenotifications' == $_GET['action']){
		check_admin_referer('wpupdate-hide-notice');
		$notices = get_option('wpupdate_notifications');
		foreach($notices as $plugin_file=>$plugin_info){
			$notices[ $plugin_file ]['HideUpdate'] = true; //Hide all the updates available.
		}
		update_option('wpupdate_notifications',$notices);
		wp_redirect($_SERVER["HTTP_REFERER"]); //Redirect back to where they came from.
	} elseif ('activate' == $_GET['action']) {
		check_admin_referer('activate-plugin_' . $_GET['plugin']);
		$current = get_option('active_plugins');
		$plugin = trim($_GET['plugin']);
		if ( validate_file($plugin) )
			wp_die(__('Invalid plugin.'));
		if ( ! file_exists(ABSPATH . PLUGINDIR . '/' . $plugin) )
			wp_die(__('Plugin file does not exist.'));
		if (!in_array($plugin, $current)) {
			wp_redirect(add_query_arg('_error_nonce', wp_create_nonce('plugin-activation-error_' . $plugin), 'plugins.php?error=true&plugin=' . $plugin)); // we'll override this later if the plugin can be included without fatal error
			ob_start();
			@include(ABSPATH . PLUGINDIR . '/' . $plugin);
			$current[] = $plugin;
			sort($current);
			update_option('active_plugins', $current);
			do_action('activate_' . $plugin);
			ob_end_clean();
		}
		wp_redirect('plugins.php?activate=true'); // overrides the ?error=true one above
	} elseif ('error_scrape' == $_GET['action']) {
		$plugin = trim($_GET['plugin']);
		check_admin_referer('plugin-activation-error_' . $plugin);
		if ( validate_file($plugin) )
			wp_die(__('Invalid plugin.'));
		if ( ! file_exists(ABSPATH . PLUGINDIR . '/' . $plugin) )
			wp_die(__('Plugin file does not exist.'));
		include(ABSPATH . PLUGINDIR . '/' . $plugin);
	} else if ('deactivate' == $_GET['action']) {
		check_admin_referer('deactivate-plugin_' . $_GET['plugin']);
		$current = get_option('active_plugins');
		array_splice($current, array_search( $_GET['plugin'], $current), 1 ); // Array-fu!
		update_option('active_plugins', $current);
		do_action('deactivate_' . trim( $_GET['plugin'] ));
		wp_redirect('plugins.php?deactivate=true');
	} elseif ('deactivate-all' == $_GET['action']) {
		check_admin_referer('deactivate-all');
		$current = get_option('active_plugins');
		update_option('deactivated_plugins', $current); 
		
		foreach ( (array)$current as $plugin) {
			if( 'wp-update/wp-update.php' == $plugin) //Slip this in to prevent this script being disabled by a mass-sweep, Else the user will loose 'Reactivate All', wnich might confuse; (We also assume that we wont break a future version here)
				continue;
			array_splice($current, array_search($plugin, $current), 1);
			do_action('deactivate_' . $plugin);
		}
		
		update_option('active_plugins', $current);
		wp_redirect('plugins.php?deactivate-all=true');
	} elseif ('reactivate-all' == $_GET['action']) { 
		//switched to this reactivate-all instead of own implementation: http://trac.wordpress.org/ticket/4176
		check_admin_referer('reactivate-all'); 
		$prev_plugins = get_option('deactivated_plugins'); 
		$current = get_option('active_plugins');
		$errors = array();
		 
		// We'll keep track of errors in the $errors array, 
		// and report them after we're done. 
		foreach ($prev_plugins as $plugin) { 
			if ( validate_file($plugin) ) {
				$errors[$plugin] = __('Invalid plugin.'); 
			} elseif ( ! file_exists(ABSPATH . PLUGINDIR . '/' . $plugin) )  {
				$errors[$plugin] = __('Plugin file does not exist.'); 
			} elseif (!in_array($plugin, $current)) { 
				// A fatal error in any one plugin means NO 
				// plugins will be reactivated. Sorry, but that's 
				// just the way it is. :-/ 
				wp_redirect('plugins.php?error=true&amp;plugin='.urlencode($plugin)); // we'll override this later if the plugin can be included without fatal error 
				$errors[$plugin] = __('Plugin generated a fatal error.'); // we'll override this later if the plugin can be included without fatal error 
				ob_start(); 
				@include(ABSPATH . PLUGINDIR . '/' . $plugin); 
				$current[] = $plugin; 
				do_action('activate_' . $plugin); 
				unset($errors[$plugin]); 
				ob_end_clean(); 
			} 
		} 
		 
		sort($current); 
		 
		update_option('deactivated_plugins', array()); 
		update_option('active_plugins', $current); 
		update_option('problem_plugins', $errors); 
		wp_redirect('plugins.php?reactivate-all=true'); // overrides the ?error=true one above 
	     
	} 
	exit;
}

add_action('admin_head','wpupdate_header');

function wpupdate_header(){ ?>
<script type="text/javascript">
//<![CDATA[
function checkUpdate(file){
	var update = 'td#wpupdate-' + file.replace('/','').replace('.','');
	$(update).html('Checking Update..');
	$(update).load('<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-update/wp-update-ajax.php?action=checkPluginUpdate&amp;file='+file);
}
//]]>
</script>
<?php
}

$title = __('Manage Plugins');
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
	<div id="message" class="updated fade"><p><?php _e('Plugin could not be activated because it triggered a <strong>fatal error</strong>.') ?></p>
	<?php
		$plugin = trim($_GET['plugin']);
		if ( wp_verify_nonce($_GET['_error_nonce'], 'plugin-activation-error_' . $plugin) && 1 == strtolower(ini_get('display_errors'))) { ?>
	<iframe style="border:0" width="100%" height="70px" src="<?php bloginfo('wpurl'); ?>/wp-admin/plugins.php?action=error_scrape&amp;plugin=<?php echo attribute_escape($plugin); ?>&amp;_wpnonce=<?php echo attribute_escape($_GET['_error_nonce']); ?>"></iframe>
	<?php
		}
	?>
	</div>
<?php elseif ( isset($_GET['activate']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('Plugin <strong>activated</strong>.') ?></p></div>
<?php elseif ( isset($_GET['deactivate']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('Plugin <strong>deactivated</strong>.') ?></p></div>
<?php elseif (isset($_GET['deactivate-all'])) : ?>
	<div id="message" class="updated fade"><p><?php _e('All plugins <strong>deactivated</strong>.'); ?></p></div>
<?php elseif (isset($_GET['reactivate-all'])) : ?> 
    <div id="message" class="updated fade"> 
	<p><?php _e('All plugins <strong>reactivated</strong>.'); ?></p> 
	<?php 
	$errors = get_option('problem_plugins'); 
	if (! empty($errors)) { 
		// Display any errors: 
		?> 
		<p><?php _e('The following plugins generated errors:'); ?></p> 
		<ul> 
		<?php foreach ($errors as $plugin => $errmsg) { 
			?> 
			<li><?php echo $plugin . ': ' . $errmsg ?></li> 
			<?php 
		} 
		?> 
		</ul> 
		<?php 
	} 
	?> 
    </div> 
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Plugin Management'); ?></h2>
<p><?php _e('Plugins extend and expand the functionality of WordPress. Once a plugin is installed, you may activate it or deactivate it here.'); ?></p>
<?php

if ( get_option('active_plugins') )
	$current_plugins = get_option('active_plugins');

$inactive = get_option('deactivated_plugins');

$plugins = wpupdate_get_plugins();
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
		<th style="text-align: center"<?php if ( current_user_can('edit_plugins') ) echo ' colspan="3"'; ?>><?php _e('Action'); ?></th>
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
		if ( is_writable(ABSPATH . PLUGINDIR . '/' . $plugin_file) && current_user_can('edit_plugins') )
			$edit = "<a href='plugin-editor.php?file=$plugin_file' title='".__('Open this file in the Plugin Editor')."' class='edit'>".__('Edit')."</a>";
		else
			$edit = '';
		
		if( current_user_can('edit_plugins') )
			$forceupdate = '<a href="#" onclick="checkUpdate(\''.$plugin_file.'\'); return false;" title="'.__('Check for Plugin Update').'" class="edit">'.__('Check for Update').'</a>';
		else
			$forceupdate = '';
		
		$updateText = '';
		if( !get_option('update_notification_enable') ){
			$updateText = __('Not Checked');
		} else {
			//Check if the plugin is disabled:
			$updateStat = $wp_update->checkPluginUpdate($plugin_file,false,false);
			if( !get_option('update_check_inactive') && 
				!in_array($plugin_file, $current_plugins) &&
				!( 
					isset($updateStat['Update']) || 
					( isset($updateStat['Errors']) && 
					  in_array('Not Found', $updateStat['Errors']) )
					)
			){
				//Plugin is disabled, and set to not check inactive plugins
				$updateText = __('Not Checked');
			} else {
				$updateText = $wp_update->getPluginUpdateText($plugin_file);
				if( false !== $updateText){
					$updateText = __($updateText);
				} else {
					$updateText = __('Please Wait');
					$updateText .= "<script type='text/javascript'>checkUpdate('$plugin_file');</script>";
				}
			}
		}
					
		echo "
	<tr $style>
		<td class='name'>{$plugin_data['Title']}</td>
		<td class='vers'>{$plugin_data['Version']}</td>
		<td class='vers' id='wpupdate-".str_replace(array('/','.'),'',$plugin_file)."'>$updateText</td>
		<td class='desc'><p>{$plugin_data['Description']} <cite>".sprintf(__('By %s'), $plugin_data['Author']).".</cite></p></td>
		<td class='togl'>$toggle</td>";
		if ( current_user_can('edit_plugins') ){
			echo "\n
			";
			if( '' != $edit )
				echo "<td>$edit</td>";
			echo "<td>$forceupdate</td>";
		}
		echo "
	</tr>";
	}

	if ( current_user_can('edit_plugins') ){ ?>
 <tr>
	<td colspan="4">&nbsp;</td>
	<td align="right">
		<?php if ( !empty($plugins) ) { ?>
		<a href="<?php echo wp_nonce_url('plugins.php?action=deactivate-all', 'deactivate-all'); ?>" class="delete"><?php _e('Deactivate All Plugins'); ?></a>
		<?php } ?>
	</td>
	<td colspan="2" align="center">
		<?php if ( !empty($inactive) ) {  ?>
		<a href="<?php echo wp_nonce_url('plugins.php?action=reactivate-all', 'reactivate-all'); ?>" class="delete"><?php _e('Reactivate All Plugins'); ?></a>
		<?php } ?>
	</td>
 </tr>
 <?php } ?>
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
?>
