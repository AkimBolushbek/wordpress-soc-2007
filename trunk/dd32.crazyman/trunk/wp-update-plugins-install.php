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
	<h2>Install a Theme</h2>
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
				var_dump($result);
			?>
		</p>
	<?php } ?>
	<?php if( (!isset($_GET['url']) || empty($_GET['url']) ) && empty($_FILES) ){ ?>
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