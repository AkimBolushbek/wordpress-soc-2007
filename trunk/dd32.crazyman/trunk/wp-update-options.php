<?php
	/* Warnings for possible bad things */
	//Enable WP_CACHE
	if( !defined('ENABLE_CACHE') || !ENABLE_CACHE){
		_e('<div class="error"><p><strong>WARNING:</strong> WordPress\'s built-in Object cache is not Enabled, You may find a performance increase if you are running on a reliable webhost.</p></div>');
	}
	
	if( isset($_POST['submit_general']) ){
		$update_notification_enable = isset($_POST['update_notification_enable']);
		$update_install_enable 		= isset($_POST['update_install_enable']);
		$update_upgrade_enable 		= isset($_POST['update_upgrade_enable']);
		$update_autoinstall_enable 	= isset($_POST['update_autoinstall_enable']);
		$update_check_inactive 		= isset($_POST['update_check_inactive']);
		
		$update_location_wordpressorg = isset($_POST['update_location_wordpressorg']);
		$update_location_custom 	= isset($_POST['update_location_custom']);
		
		$update_autocheck_nightly	= isset($_POST['update_autocheck_nightly']);
		$update_email_enable 		= isset($_POST['update_email_enable']);
		$update_email_email 		= isset($_POST['update_email_email']) ? $_POST['update_email_email'] : get_option('update_email_email');
		
		$update_plugin_search_enable = isset($_POST['update_plugin_search_enable']);
		$update_theme_search_enable = isset($_POST['update_theme_search_enable']);

		update_option('update_notification_enable',	$update_notification_enable);
		update_option('update_install_enable',		$update_install_enable);
		update_option('update_upgrade_enable',		$update_upgrade_enable);
		update_option('update_autoinstall_enable',	$update_autoinstall_enable);
		update_option('update_check_inactive',		$update_check_inactive);
		update_option('update_location_wordpressorg',$update_location_wordpressorg);
		update_option('update_location_wppluginsnet',$update_location_wppluginsnet);
		update_option('update_location_custom',		$update_location_custom);
		update_option('update_autocheck_nightly',	$update_autocheck_nightly);
		update_option('update_email_enable',		$update_email_enable);
		update_option('update_email_email',			$update_email_email);
		update_option('update_plugin_search_enable',$update_plugin_search_enable);
		update_option('update_theme_search_enable',	$update_theme_search_enable);
		echo '<div class="updated"><p>' . __('General Options Saved') . '.</p></div>';
	}
	
?>
<div class="wrap">
	<h2>General Options</h2>
	<form method="post">
	<p>
		<h3>General Options</h3>
		<input type="checkbox" name="update_notification_enable" <?php checked(true,get_option('update_notification_enable')) ?> />
		<?php _e('Enable Plugin Notifications'); ?><br />
		<input type="checkbox" name="update_install_enable" <?php checked(true,get_option('update_install_enable')) ?> />
		<?php _e('Enable Installing of Plugins and Themes'); ?><br />
		<input type="checkbox" name="update_upgrade_enable" <?php checked(true,get_option('update_upgrade_enable')) ?> />
		<?php _e('Enable Upgrading of Plugins and Themes'); ?><br />
		<input type="checkbox" name="update_autoinstall_enable" <?php checked(true,get_option('update_autoinstall_enable')) ?> disabled="disabled" />
		<?php _e('Enable of Auto-Installing of Plugin and Theme updates'); ?><br />
		<input type="checkbox" name="update_check_inactive" <?php checked(true,get_option('update_check_inactive')) ?> />
		<?php _e('Enable checking for Plugins which are NOT Activated'); ?><br />
		<input type="checkbox" name="update_plugin_search_enable" <?php checked(true,get_option('update_plugin_search_enable')) ?> />
		<?php _e('Enable Plugin Search'); ?><br />
		<input type="checkbox" name="update_theme_search_enable" <?php checked(true,get_option('update_theme_search_enable')) ?> />
		<?php _e('Enable Theme Search'); ?><br />
	</p>
	<p>
		<h3>Plugin/Theme Update Options</h3>
		<input type="checkbox" name="update_location_wordpressorg" <?php checked(true,get_option('update_location_wordpressorg')) ?> />
		<?php _e('Enable Update notifications from Wordpres.Org/extend/'); ?><br />
		<input type="checkbox" name="update_location_custom" <?php checked(true,get_option('update_location_custom'))?> />
		<?php _e('Enable Update notifications from Plugin-Specific sites'); ?><br />
	</p>
	<p>
		<h3>Update Options</h3>
		<input type="checkbox" name="update_autocheck_nightly" <?php checked(true,get_option('update_autocheck_nightly')) ?> />
		<?php _e('Check for Plugin updated Nightly'); ?><br />
		<input type="checkbox" name="update_email_enable" <?php checked(true,get_option('update_email_enable'))?> />
		<?php _e('Email Blog Owner when updates are available:'); ?>
		<input type="textbox" name="update_email_email" <?php if(false !== ($email = get_option('update_email_email')) ){ echo 'value="'.$email.'"'; } ?> />
	</p>
	<p class="submit">
		<input type="submit" name="submit_general" value="<?php _e('Save Options &raquo;'); ?>" />
	</p>
	</form>
</div>

<?php
	if( is_writable(ABSPATH . '/wp-content/plugins/') ){
		if( getmyuid() == fileowner(__FILE__) ) //If owner of this current file is the same as the current username
			echo '<div class="updated"><p>' . __('<strong>Note:</strong>With the current server configuration, <strong>"Direct"</strong> Filesystem access is recomended.') . '</p></div>';
		else
			echo '<div class="updated"><p>' . __('<strong>Note:</strong>With the current server configuration, <strong>"FTP"</strong> Filesystem access is recomended.') . '</p></div>';
	}
	
	
	
?>


<div class="wrap">
	<h2> Filesystem Options </h2>
	<form name="filesystem" method="post">
	<strong>FTP:</strong> <input type="radio" name="filesystem[type]" value="ftp" onchange="$('#filesystem-ftp').show(); $('#filesystem-direct').hide();" /> &nbsp;
	<strong>Direct:</strong> <input type="radio" name="filesystem[type]" value="direct" onchange="$('#filesystem-direct').show(); $('#filesystem-ftp').hide();" /><br />
	
	<div class="section" id="filesystem-ftp">
	<script type="text/javascript">
		function filesystem_ftp_detect(){
			$.post('<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-update/wp-update-ajax.php?action=filesystem_get_ftp_path',
			  { hostname: $('input[@name="filesystem[ftp][hostname]"]').val(), 
			  	username: $('input[@name="filesystem[ftp][username]"]').val(), 
				password: $('input[@name="filesystem[ftp][password]"]').val(), 
				ssl: $('input[@name="filesystem[ftp][ssl]"]').attr('checked'),
				method: $('select[@name="filesystem[ftp][method]"]').val()  },
			  function(json){
				alert("JSON Data: " + json);
			  }
			);
		}
	</script>
		<h3>FTP Options</h3>
		<strong>Hostname:</strong><input type="text" name="filesystem[ftp][hostname]"  /><br />
		<strong>Username:</strong><input type="text" name="filesystem[ftp][username]" /><br />
		<strong>Password:</strong><input type="password" name="filesystem[ftp][password]" />&nbsp;
								  <input type="checkbox" name="filesystem[ftp][passwordsave]" />Save Password<br />
		<strong>Base Directory:</strong><input type="text" name="filesystem[ftp][basedir]" />&nbsp;<input type="button" value="Automatically Detect" onclick="filesystem_ftp_detect();" /><br />
		
		<h3>Connection Options</h3>
		<input type="checkbox" name="filesystem[ftp][ssl]" /> Secure connection <em>(sFTP)</em><br />
		FTP Connection: <select name="filesystem[ftp][method]" >
							<option value="phpext"<?php if( !extension_loaded('ftp') ){ echo ' disabled="disabled"';} ?>>PHP FTP Extension</option>
							<option value="phpsockets"<?php if( !extension_loaded('sockets') ){ echo ' disabled="disabled"';} ?>>PHP Sockets</option>
							<option value="phpstream"<?php if( !function_exists('fsockopen') ){ echo ' disabled="disabled"';} ?>>PHP Stream Sockets</option>
						</select>
		
	</div>
	<div class="section" id="filesystem-direct">
		<h3>Direct Access Options</h3>
		<strong>Base Directory:</strong><input type="text" name="filesystem[direct][basedir]" id="fs-direct-base" value="" />&nbsp;
						<input type="button" value="Reset" onclick="$('#fs-direct-base').val('<?php echo attribute_escape(addslashes(ABSPATH)); ?>');" /><br />		
	</div>
	
	<p class="submit">
		<input type="submit" name="submit_filesystem" value="<?php _e('Save Filesystem Information &raquo;') ?>" />
	</p>
	</form>
</div>


<?php
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
		echo '<div class="updated"><p>' . __('FTP Options Saved') . '</p></div>';
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