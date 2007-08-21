<?php
if( !get_option('update_install_enable') ){
	echo '<div class="error"><h1>Not Enabled</h1></div>';
	return;
}
require_once('includes/wp-update-class.php');
global $wp_update;
if( ! $wp_update || ! is_object($wp_update) )
	$wp_update = new WP_Update;
?>
<style>
	.section{
		margin-left:2em;
	}
</style>
<div class="wrap">
	<h2><?php _e('Install a Theme'); ?></h2>
<?php 
if( !empty($_FILES) || !empty($_GET['url']) ){ 
	check_admin_referer('wpupdate-theme-install');
	if( isset($_GET['url']) ){
		//Download file
		$filename = attribute_escape($_GET['url']); //TODO?
	} elseif ( ! empty($_FILES['themefile']['tmp_name']) ) {
		$filename = $_FILES['themefile']['name'];
	}
?>
	<h3><?php _e('Step 2'); ?></h3>
	<div class="section">
		<h4><?php _e('Installing..'); ?></h4>
		<p>
			<?php _e('Filename'); ?>: <strong><?php echo basename($filename); ?></strong><br />
			<?php if( isset($_GET['url']) ){ ?>
				<?php _e('Source'); ?>: <strong><?php echo $_GET['url']; ?></strong><br />
			<?php } ?>
			<?php
				if( $_FILES ){
					$file = $_FILES['themefile']['tmp_name'];
					$fileinfo = $_FILES['themefile'];
				} elseif( isset($_GET['url']) ){
					$file = wpupdate_url_to_file($_GET['url']);
					$fileinfo = pathinfo($_GET['url']);
					$fileinfo['name'] = $fileinfo['basename'];
				} else {
					wp_die(__('Unsupported Method Called'));
				}
				$result = $wp_update->installTheme($file,$fileinfo);
				if( isset($result['Error']) ){
					echo '<div class="error">' . __('Errors Occured') . ':<br />' . implode('<br />', $result['Error']) . '</div>';
				}
				unset($result['Error']);
				foreach((array)$result as $message){
					echo $message . '<br />';
				}
			?>
		</p>
	</div>
	<p>
		<?php _e('If you see no errors mentioned above, then your theme is now installed, and may be <a href="themes.php">activated</a>.'); ?>
	</p>
<?php
}
?>
<?php if( empty($_GET['url']) && empty($_FILES) ){ ?>
	<h3><?php _e('Step 1:'); ?></h3>
		<div class="section">
			<h4><?php _e('Upload file'); ?></h4>
			<p>
			<form enctype="multipart/form-data" name="installlocalfile" method="POST" action="<?php echo $pagenow . '?page=' . $_GET['page'] ?>">
			<?php wp_nonce_field('wpupdate-theme-install'); ?>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ((int)ini_get('post_max_size'))*1024*1024; ?>" />
			<?php _e('Select File'); ?>: <input type="file" name="themefile" />&nbsp;<input type="submit" name="submit" value="<?php _e('Upload &raquo;'); ?>" /><br />
			<strong><?php _e('Max filesize'); ?>:</strong><?php echo ini_get('post_max_size'); ?>
			</form>
			<?php _e('OR'); ?>
			<h4><?php _e('Via a Search'); ?></h4>
			<p><?php _e('To install themes directly without uploading them yourself, Please use the <a href="themes.php?page=wp-update/wp-update-themes-search.php">Theme Search</a> tab, and select "Install" on the item.'); ?></p>
			</p>
		</div>
<?php } /* end if empty && empty */?>
</div>