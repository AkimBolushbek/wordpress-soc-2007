<?php
require_once('includes/wp-update-class.php');
global $wpupdate;
$wpupdate = new WP_Update;

?>
<?php
	/* Warnings for possible bad things */
	//Enable WP_CACHE
	if( !defined('ENABLE_CACHE') || !ENABLE_CACHE){
		echo '<div class="error"><p><strong>WARNING:</strong> WordPress\'s built-in Object cache is not Enabled, You may find a performance increase if you are running on a reliable webhost.</p></div>';
	}
	//Cache folder server writable? I guess i can rely on the TMP folders..
	if( !is_writable(ABSPATH . '/wp-content/cache/') ){
		echo '<div class="error"><p><strong>WARNING:</strong>Your wp-content/cache/ directory is NOT writable, WordPress relies on this folder for storing Cache data.</p></div>';
	}
?>
<div class="wrap">
	<h2>General Options</h2>
	<p class="submit">
		<input type="submit" name="submit_general" value="Save Options &raquo;" />
	</p>
</div>

<?php
	if( is_writable(ABSPATH . '/wp-content/cache/') ){
		$testfile = ABSPATH . '/wp-content/cache/test.file';
		if( false !== ($fp = @fopen($testfile,'w')) ){
			fclose($fp);
			if( fileowner($testfile) == fileowner(__FILE__) ) //If owner of this current file is the same as the user who we just created a file with
				echo '<div class="updated"><p><strong>Note:</strong>FTP is NOT needed due to the current server configuration. You <strong>MAY</strong> still use this if you wish.</p></div>';	
			unlink($testfile); //Delete test file.
		}
	}
	
	if( isset($_POST['submit_ftp']) ){
		$ftpinfo = array(
						'host' => $_POST['ftp_host'],
						'user' => $_POST['ftp_user'],
						'dir'  => $_POST['ftp_dir']
						);
		if( 'on' == $_POST['ftp_pass_save'] ){
			$ftpinfo['pass'] = $_POST['ftp_pass'];
		}
		
		update_option('wpupdate_ftp',$ftpinfo);
	}
	$ftpinfo = get_option('wpupdate_ftp');
	var_dump($ftpinfo);
	var_dump($_POST);
?>
<div class="wrap">
	<h2>FTP Options</h2>
	<form method="POST">
	<fieldset>
		<p>
		<label for="ftp_host"><strong>Hostname:</strong></label><input type="text" name="ftp_host" value="<?php echo $ftpinfo['host']; ?>" /><br />
		<label for="ftp_user"><strong>Username:</strong></label><input type="text" name="ftp_user" value="<?php echo $ftpinfo['user']; ?>" /><br />
		<label for="ftp_dir"><strong>Directory:</strong></label><input type="text" name="ftp_dir" value="<?php echo $ftpinfo['dir']; ?>" /><br />
		<label for="ftp_pass"><strong>Password:</strong></label><input type="password" name="ftp_pass" value="<?php if('' != $ftpinfo['pass']){ echo '*********"'; } ?>" /> &nbsp; ( <label for="ftp_pass_save">Save Password:</label>&nbsp;<input type="checkbox" name="ftp_pass_save" <?php if('' != $ftpinfo['pass']){ echo 'checked="checked"'; } ?> />)<br />
		</p>
		<p class="submit">
			<input type="submit" name="submit_ftp" value="Save FTP Information &raquo;" />
		</p>
	</fieldset>
	</form>
</div>