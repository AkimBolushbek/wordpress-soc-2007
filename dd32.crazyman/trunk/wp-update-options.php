<?php
	/* Warnings for possible bad things */
	//Enable WP_CACHE
	if( !defined('ENABLE_CACHE') || !ENABLE_CACHE){
		_e('<div class="error"><p><strong>WARNING:</strong> WordPress\'s built-in Object cache is not Enabled, You may find a performance increase if you are running on a reliable webhost.</p></div>');
	}
	
	if( isset($_POST['submit_general']) ){
		$update_notification_enable = isset($_POST['update_notification_enable']) ? true : false;
		$update_install_enable 		= isset($_POST['update_install_enable']) ? true : false;
		$update_upgrade_enable 		= isset($_POST['update_upgrade_enable']) ? true : false;
		$update_autoinstall_enable 	= isset($_POST['update_autoinstall_enable']) ? true : false;
		$update_check_inactive 		= isset($_POST['update_check_inactive']) ? true : false;
		
		$update_location_wordpressorg = isset($_POST['update_location_wordpressorg']) ? true : false;
		$update_location_custom 	= isset($_POST['update_location_custom']) ? true : false;
		
		$update_autocheck_nightly	= isset($_POST['update_autocheck_nightly']) ? true : false;
		$update_email_enable 		= isset($_POST['update_email_enable']) ? true : false;
		$update_email_email 		= isset($_POST['update_email_email']) ? $_POST['update_email_email'] : get_option('update_email_email');
		
		$update_plugin_search_enable = isset($_POST['update_plugin_search_enable']) ? true : false;
		$update_theme_search_enable = isset($_POST['update_theme_search_enable']) ? true : false;

		update_option('update_notification_enable',$update_notification_enable);
		update_option('update_install_enable',$update_install_enable);
		update_option('update_upgrade_enable',$update_upgrade_enable);
		update_option('update_autoinstall_enable',$update_autoinstall_enable);
		update_option('update_check_inactive',$update_check_inactive);
		update_option('update_location_wordpressorg',$update_location_wordpressorg);
		update_option('update_location_wppluginsnet',$update_location_wppluginsnet);
		update_option('update_location_custom',$update_location_custom);
		update_option('update_autocheck_nightly',$update_autocheck_nightly);
		update_option('update_email_enable',$update_email_enable);
		update_option('update_email_email',$update_email_email);
		update_option('update_plugin_search_enable',$update_plugin_search_enable);
		update_option('update_theme_search_enable',$update_theme_search_enable);
		_e('<div class="updated"><p>General Options Saved.</p></div>');
	}
	
?>
<div class="wrap">
	<h2>General Options</h2>
	<form method="post">
	<p>
		<h3>General Options</h3>
		<input type="checkbox" name="update_notification_enable" <?php if(get_option('update_notification_enable')){ echo 'checked="checked"'; } ?> />
		<?php _e('Enable Plugin Notifications'); ?><br />
		<input type="checkbox" name="update_install_enable" <?php if(get_option('update_install_enable')){ echo 'checked="checked"'; } ?> />
		<?php _e('Enable Installing of Plugins and Themes'); ?><br />
		<input type="checkbox" name="update_upgrade_enable" <?php if(get_option('update_upgrade_enable')){ echo 'checked="checked"'; } ?> />
		<?php _e('Enable Upgrading of Plugins and Themes'); ?><br />
		<input type="checkbox" name="update_autoinstall_enable" <?php if(get_option('update_autoinstall_enable')){ echo 'checked="checked"'; } ?> disabled="disabled" />
		<?php _e('Enable of Auto-Installing of Plugin and Theme updates'); ?><br />
		<input type="checkbox" name="update_check_inactive" <?php if(get_option('update_check_inactive')){ echo 'checked="checked"'; } ?> />
		<?php _e('Enable checking for Plugins which are NOT Activated'); ?><br />
		<input type="checkbox" name="update_plugin_search_enable" <?php if(get_option('update_plugin_search_enable')){ echo 'checked="checked"'; } ?> />
		<?php _e('Enable Plugin Search'); ?><br />
		<input type="checkbox" name="update_theme_search_enable" <?php if(get_option('update_theme_search_enable')){ echo 'checked="checked"'; } ?> />
		<?php _e('Enable Theme Search'); ?><br />
	</p>
	<p>
		<h3>Plugin/Theme Update Options</h3>
		<input type="checkbox" name="update_location_wordpressorg" <?php if(get_option('update_location_wordpressorg')){ echo 'checked="checked"'; } ?> />
		<?php _e('Enable Update notifications from Wordpres.Org/extend/'); ?><br />
		<input type="checkbox" name="update_location_custom" <?php if(get_option('update_location_custom')){ echo 'checked="checked"'; } ?> />
		<?php _e('Enable Update notifications from Plugin-Specific sites'); ?><br />
	</p>
	<p>
		<h3>Update Options</h3>
		<input type="checkbox" name="update_autocheck_nightly" <?php if(get_option('update_autocheck_nightly')){ echo 'checked="checked"'; } ?> />
		<?php _e('Check for Plugin updated Nightly'); ?><br />
		<input type="checkbox" name="update_email_enable" <?php if(get_option('update_email_enable')){ echo 'checked="checked"'; } ?> />
		<?php _e('Email Blog Owner when updates are available:'); ?>
		<input type="textbox" name="update_email_email" <?php if(false !== ($email = get_option('update_email_email')) ){ echo 'value="'.$email.'"'; } ?> />
	</p>
	<p class="submit">
		<input type="submit" name="submit_general" value="<?php _e('Save Options &raquo;'); ?>" />
	</p>
	</form>
</div>

<?php
	if( is_writable(ABSPATH . '/wp-content/cache/') ){
		if( getmyuid() == fileowner(__FILE__) ) //If owner of this current file is the same as the user who we just created a file with
			_e('<div class="updated"><p><strong>Note:</strong>FTP is NOT needed due to the current server configuration. You <strong>MAY</strong> still use this if you wish.</p></div>');	
	}
	
	if( isset($_POST['submit_ftp']) ){
		$ftpinfo = array(
						'host' => $_POST['ftp_host'],
						'username' => $_POST['ftp_user'],
						'base'  => $_POST['ftp_dir'],
						'ssl' => $_POST['ftp_host_ssl']
						);
		if( 'on' == $_POST['ftp_pass_save'] && !empty($_POST['ftp_pass']) ){
			$ftpinfo['password'] = $_POST['ftp_pass'];
		}
		
		update_option('wpupdate_ftp',$ftpinfo);
		_e('<div class="updated"><p>FTP Options Saved.</p></div>');
		var_dump($ftpinfo);
	}
	$ftpinfo = get_option('wpupdate_ftp');
?>
<div class="wrap">
	<h2>FTP Options</h2>
	<form method="POST">
	<fieldset>
		<p>
		<label for="ftp_host"><strong><?php _e('Hostname:') ?></strong></label><input type="text" name="ftp_host" value="<?php echo $ftpinfo['host']; ?>" /> &nbsp; (
		<label for="ftp_host_ssl"><?php _e('SFTP:') ?></label>&nbsp;<input type="checkbox" name="ftp_host_ssl" <?php checked('1',$ftpinfo['ssl']); ?> />)<br />
		<label for="ftp_user"><strong><?php _e('Username:') ?></strong></label><input type="text" name="ftp_user" value="<?php echo $ftpinfo['username']; ?>" /><br />
		<label for="ftp_dir"><strong><?php _e('Directory:') ?></strong></label><input type="text" name="ftp_dir" value="<?php echo $ftpinfo['base']; ?>" /><br />
		<label for="ftp_pass"><strong><?php _e('Password:') ?></strong></label><input type="password" name="ftp_pass" value="" /> &nbsp; ( <label for="ftp_pass_save"><?php _e('Save Password:') ?></label>&nbsp;<input type="checkbox" name="ftp_pass_save" <?php if('' != $ftpinfo['password']){ echo 'checked="checked"'; } ?> />)<br />
		</p>
		<p class="submit">
			<input type="submit" name="submit_ftp" value="<?php _e('Save FTP Information &raquo;') ?>" />
		</p>
	</fieldset>
	</form>
</div>