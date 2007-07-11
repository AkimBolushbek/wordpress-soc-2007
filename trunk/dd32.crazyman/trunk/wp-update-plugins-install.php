<?php
if( !get_option('update_install_enable') ){
	echo '<div class="error"><h1>Not Enabled</h1></div>';
	return;
}
	
require_once('includes/wp-update-class.php');
global $wpupdate;
$wpupdate = new WP_Update;
?>
<style type="text/css">

</style>
<div class="wrap">
	<h2>Install a Plugin</h2>
	<?php
		//First we check to see if this is a WordPress.org plugin, We'll have some requirements to check first :)
		if ( isset($_GET['wp-id']) && !empty($_GET['wp-id']) && !isset($_GET['proceed']) ) {
			$id = $_GET['wp-id'];
			$pluginInfo = $wpupdate->checkPluginUpdateWordpressOrg($id);
			?>
				<p>
					<?php _e('Are you sure you wish to install the following Plugin?'); ?>
				</p>
					<?php
						$pluginCompatible = $wpupdate->checkPluginCompatible($pluginInfo);
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
					<strong>Plugin Name: </strong><?php echo $pluginInfo['Name'] . ' ' . $pluginInfo['Version']; ?><br />
					<strong>Author:</strong> <?php echo empty($pluginInfo['AuthorHome']) ? 
														$pluginInfo['Author'] : 
														'<a target="_blank" href="' . $pluginInfo['AuthorHome'] . '">' . $pluginInfo['Author'] . '</a>' ?><br/>
					<strong>Last Updated:</strong> <?php echo $pluginInfo['LastUpdate']; ?><br />
					<strong>Plugin Homepage:</strong> <?php echo '<a target="_blank" href="' . $pluginInfo['PluginHome'] . '">' . $pluginInfo['PluginHome'] . '</a>' ?><br />
					<strong>Plugin Rating:</strong> <?php echo $pluginInfo['Rating']; ?>%<br />
					<strong>Download:</strong> <?php echo '<a target="_blank" href="' . $pluginInfo['Download'] . '">' . $pluginInfo['Download'] . '</a>' ?><br/>
					<strong>Tags:</strong><?php
												foreach( (array) $pluginInfo['Tags'] as $tag){
													echo '<a href="plugins.php?page=wp-update/wp-update-plugins-search.php&tag=' . $tag . '">' . 
															str_replace('-',' ',$tag) . '</a> ';
												}
												?><br />
					<strong>Related Plugins:</strong><?php
												foreach( (array) $pluginInfo['Related'] as $related){
													echo '<a href="plugins.php?page=wp-update%2Fwp-update-plugins-install.php&wp-id=' . $related . '">' . 
														str_replace('-',' ',$related) . '</a> ';
												}
												?><br/>
				</p>
				<p class="submit">
				<form action="<?php echo $pagenow ?>" method="GET">
					<input type="hidden" name="page" value="<?php echo attribute_escape($_GET['page']); ?>" />
					<input type="hidden" name="url" value="<?php echo attribute_escape($pluginInfo['Download']); ?>" />
					<input type="hidden" name="proceed" value="y" />
					<input type="submit" name="submit" value="<?php _e('Ok, Install the plugin &raquo;') ?>" />
				</form>
				</p>
			<?php
		}//end if wp-id
	?>
	<?php if( (isset($_GET['url']) && !empty($_GET['url']) ) || !empty($_FILES) ){
		if( isset($_GET['url']) ){
			//Download file
			$filename = attribute_escape($_GET['url']); //TODO?
		} elseif ( '' != $_FILES['pluginfile']['tmp_name'] ) {
			$filename = $_FILES['pluginfile']['name'];
		}

	?>
		<h3>Installing..</h3>
		<p>
			Filename: <strong><?php echo basename($filename); ?></strong><br />
			<?php if( isset($_GET['url']) ){ ?>
			Source: <strong><?php echo $_GET['url']; ?></strong><br />
			<?php } ?>
			<?php
				$file = $_FILES['pluginfile']['tmp_name'];
				$fileinfo = $_FILES['pluginfile'];
				$result = $wpupdate->installPlugin($file,$fileinfo);
				if( isset($result['Error']) ){
					echo '<div class="error">' . __('Errors Occured:') . '<br />' . implode('<br />', $result['Error']) . '</div>';
				}
				unset($result['Error']);
				foreach((array)$result as $message){
					echo $message . '<br />';
				}
				var_dump($result);
			?>
		</p>
	<?php } ?>
	<?php if( (!isset($_GET['url']) || empty($_GET['url']) ) && empty($_FILES) && (!isset($_GET['wp-id']) || empty($_GET['wp-id'])) ){ ?>
		<h3>Upload file</h3>
		<p>
			<form enctype="multipart/form-data" name="installlocalfile" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ((int)ini_get('post_max_size'))*1024*1024; ?>" />
				Select File: <input type="file" name="pluginfile" />&nbsp;<input type="submit" name="submit" value="Upload &raquo;" /><br />
				<strong>Max filesize:</strong><?php echo ini_get('post_max_size'); ?>
			</form>
		</p>
		<h3>From URL</h3>
		<p>
			<form enctype="multipart/form-data" name="installurl" method="GET">
			<input type="hidden" name="page" value="<?php echo attribute_escape($_GET['page']); ?>" />
			URL: <input type="text" name="url" /> &nbsp;
				<input type="submit" name="submit" value="Install &raquo;" />
			</form>
		</p>
		<h3>Wordpress Plugin ID</h3>
		<p>
			<form enctype="multipart/form-data" name="installurl" method="GET">
			<input type="hidden" name="page" value="<?php echo attribute_escape($_GET['page']); ?>" />
			ID: <input type="text" name="wp-id" /> &nbsp;
				<input type="submit" name="submit" value="Install &raquo;" />
			</form>
		</p>
		<h3>Via a Search</h3>
			<p>To install themes directly without uploading them yourself, Please use the <a href="themes.php?page=wp-update/wp-update-plugin-search.php">Plugin Search</a> tab, and select "Install" on the item.</p>
		</p>
	<?php } ?>
</div>