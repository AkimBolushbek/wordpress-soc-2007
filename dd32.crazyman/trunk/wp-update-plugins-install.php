<?php
if( !get_option('update_install_enable') || !defined('ABSPATH') ){
	echo '<div class="error"><h1>Not Enabled</h1></div>';
	return;
}

require_once('includes/wp-update-class.php');
global $wp_update;
if( ! $wp_update || ! is_object($wp_update) )
	$wp_update = new WP_Update;
if( isset($_GET['upgrade']) || isset($_POST['upgrade']) ){
	//Lets rely on a sepate file for upgrade proceedure
	include 'wp-update-plugins-upgrade.php';
	return;
}

?>
<div class="wrap">
	<h2><?php _e('Install a Plugin'); ?></h2>
	<?php
		//First we check to see if this is a WordPress.org plugin, We'll have some requirements to check first :)
		if ( isset($_GET['wp-id']) && !empty($_GET['wp-id']) && !isset($_GET['proceed']) ) {
			check_admin_referer('wpupdate-install-plugin');
			$id = $_GET['wp-id'];
			$pluginInfo = apply_filters('wpupdate_checkPluginUpdate-wordpress.org',$id);
			?>
				<p>
					<?php _e('Are you sure you wish to install the following Plugin?'); ?>
				</p>
					<?php
						$pluginCompatible = $wp_update->checkPluginCompatible($pluginInfo);
						if( $pluginCompatible['Compatible'] ){
							echo '<div class="updated"><p>'. __('Plugin is compatible') . '</p></div>';
						} else {
							echo '<div class="error"><p>'. __('Plugin is not compatible') . '</p></div>';
						}
						if( isset($pluginCompatible['Errors']) ){
							echo '<div class="error"><p>';
							foreach( (array) $pluginCompatible['Errors'] as $message) {
								echo $message . "<br />";
							}
							echo '</p></div>';
						}
					?>
				<p>
					<strong><?php _e('Plugin'.' Name'); ?>: </strong><?php echo $pluginInfo['Name'] . ' ' . $pluginInfo['Version']; ?><br />
					<strong><?php _e('Author'); ?>:</strong> <?php echo empty($pluginInfo['AuthorHome']) ? 
														$pluginInfo['Author'] : 
														'<a target="_blank" href="' . $pluginInfo['AuthorHome'] . '">' . $pluginInfo['Author'] . '</a>' ?><br/>
					<strong><?php _e('Last Updated'); ?>:</strong> <?php echo $pluginInfo['LastUpdate']; ?><br />
					<strong><?php _e('Plugin Homepage'); ?>:</strong> <?php echo '<a target="_blank" href="' . $pluginInfo['PluginHome'] . '">' . $pluginInfo['PluginHome'] . '</a>' ?><br />
					<strong><?php _e('Plugin Rating'); ?>:</strong> <?php echo $pluginInfo['Rating']; ?>%<br />
					<strong><?php _e('Download'); ?>:</strong> <?php echo '<a target="_blank" href="' . $pluginInfo['Download'] . '">' . $pluginInfo['Download'] . '</a>' ?><br/>
					<strong><?php _e('Tags'); ?>:</strong><?php
												foreach( (array) $pluginInfo['Tags'] as $tag){
													echo '<a href="plugins.php?page=wp-update/wp-update-plugins-search.php&amp;tag=' . $tag . '">' . 
															str_replace('-',' ',$tag) . '</a> ';
												}
												?><br />
					<strong><?php _e('Related Plugins'); ?>:</strong><?php
												foreach( (array) $pluginInfo['Related'] as $related){
													echo '<a href="plugins.php?page=wp-update%2Fwp-update-plugins-install.php&amp;wp-id=' . $related . '">' . 
														str_replace('-',' ',$related) . '</a> ';
												}
												?><br/>
				</p>
				<p class="submit">
				<form action="<?php echo $pagenow . '?page=' . $_GET['page']; ?>" method="GET">
					<?php wp_nonce_field('wpupdate-install-plugin'); ?>
					<input type="hidden" name="url" value="<?php echo attribute_escape($pluginInfo['Download']); ?>" />
					<input type="hidden" name="proceed" value="y" />
					<input type="submit" name="submit" value="<?php _e('Ok, Install the plugin &raquo;') ?>" />
				</form>
				</p>
			<?php
		}//end if wp-id
	?>
	<?php if( (isset($_GET['url']) && !empty($_GET['url']) ) || !empty($_FILES) ){
		check_admin_referer('wpupdate-install-plugin');
		if( isset($_GET['url']) ){
			//Download file
			$filename = attribute_escape($_GET['url']); //TODO?
		} elseif ( '' != $_FILES['pluginfile']['tmp_name'] ) {
			$filename = $_FILES['pluginfile']['name'];
		}

	?>
		<h3><?php _e('Installing'); ?>..</h3>
		<p>
			<?php _e('Filename'); ?>: <strong><?php echo basename($filename); ?></strong><br />
			<?php if( isset($_GET['url']) ){ ?>
				<?php _e('Source'); ?>: <strong><?php echo $_GET['url']; ?></strong><br />
			<?php } ?>
			<?php
				if( $_FILES ){
					$file = $_FILES['pluginfile']['tmp_name'];
					$fileinfo = $_FILES['pluginfile'];
				} elseif( isset($_GET['url']) ){
					$file = wpupdate_url_to_file($_GET['url']);
					$fileinfo = pathinfo($_GET['url']);
					$fileinfo['name'] = $fileinfo['basename'];
				} else {
					wp_die(__('Unsupported Method Called'));
				}

				$result = $wp_update->installPlugin($file,$fileinfo);

				if( isset($result['Error']) ){
					echo '<div class="error">' . __('Errors Occured') . ':<br />' . implode('<br />', $result['Error']) . '</div>';
				}
				unset($result['Error']);
				foreach((array)$result as $message){
					echo $message . '<br />';
				}
			?>
		</p>
	<?php } ?>
	<?php if( (!isset($_GET['url']) || empty($_GET['url']) ) && empty($_FILES) && (!isset($_GET['wp-id']) || empty($_GET['wp-id'])) ){ ?>
		<h3><?php _e('Upload file'); ?></h3>
		<p>
			<form enctype="multipart/form-data" name="installlocalfile" method="POST" action="<?php echo $pagenow . '?page=' . $_GET['page'] ?>">
				<?php wp_nonce_field('wpupdate-install-plugin'); ?>
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ((int)ini_get('post_max_size'))*1024*1024; ?>" />
				<?php _e('Select File'); ?>: <input type="file" name="pluginfile" />&nbsp;<input type="submit" name="submit" value="<?php _e('Upload &raquo;'); ?>" /><br />
				<strong><?php _e('Max filesize'); ?>:</strong><?php echo ini_get('post_max_size'); ?>
			</form>
		</p>
		<h3><?php _e('From URL'); ?></h3>
		<p>
			<form enctype="multipart/form-data" name="installurl" method="GET" action="<?php echo $pagenow . '?page=' . $_GET['page'] ?>">
			<?php wp_nonce_field('wpupdate-install-plugin'); ?>
			<input type="hidden" name="page" value="<?php echo attribute_escape($_GET['page']); ?>" />
			<?php _e('URL'); ?>: <input type="text" name="url" /> &nbsp;
				<input type="submit" name="submit" value="<?php _e('Install &raquo;'); ?>" />
			</form>
		</p>
		<h3><?php _e('Wordpress Plugin ID'); ?></h3>
		<p>
			<form enctype="multipart/form-data" name="installurl" method="GET" action="<?php echo $pagenow . '?page=' . $_GET['page'] ?>">
			<?php wp_nonce_field('wpupdate-install-plugin'); ?>
			<input type="hidden" name="page" value="<?php echo attribute_escape($_GET['page']); ?>" />
			ID: <input type="text" name="wp-id" /> &nbsp;
				<input type="submit" name="submit" value=""<?php _e('Install &raquo;'); ?>" />
			</form>
		</p>
		<h3><?php _e('Via a Search'); ?></h3>
			<p><?php sprintf(__('To install themes directly without uploading them yourself, Please use the <a href="%s">Plugin Search</a> tab, and select "Install" on the item.'), 'themes.php?page=wp-update/wp-update-plugin-search.php'); ?></p>
		</p>
	<?php } ?>
</div>