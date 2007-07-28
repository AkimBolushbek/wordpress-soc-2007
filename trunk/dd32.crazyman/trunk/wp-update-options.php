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
		
		$update_location_search		= isset($_POST['update_location_search']);
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
		update_option('update_location_search',		$update_location_search);
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
		<input type="checkbox" name="update_location_search" <?php checked(true,get_option('update_location_search')) ?> />
		<?php _e('Enable Searching for Plugin update notifications'); ?><br />
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
	if( isset($_POST['submit_filesystem']) ){
		var_dump($_POST);
		if( 'direct' == $_POST['filesystem']['type'] )
			update_option('wpfs_method','direct');
		else
			update_option('wpfs_method',$_POST['filesystem']['ftp']['method']);

		$ftp = array();
		$ftp['method'] = $_POST['filesystem']['ftp']['method'];
		$ftp['hostname'] = $_POST['filesystem']['ftp']['hostname'];
		$ftp['username'] = $_POST['filesystem']['ftp']['username']; //Underscores have been used to prevent browser "wands" from attacking the field
		$ftp['password'] = false;
		$ftp['passwordsave'] = false;
		$ftp['basedir'] = $_POST['filesystem']['ftp']['basedir'];
		$ftp['ssl'] = isset($_POST['filesystem']['ftp']['ssl']);		

		if( isset($_POST['filesystem']['ftp']['passwordsave']) ){
			$password = $_POST['filesystem']['ftp']['password'];
			$_ftp = get_option('wpfs_ftp');
			if ( '********' == $password || empty($password) )
				$password = $_ftp['password'];
			$ftp['password'] = $password;
		} 
		update_option('wpfs_ftp',$ftp);
		var_dump($ftp);
	}//end if filesystem 
	
	$method = 'direct' == get_option('wpfs_method') ? 'direct' : 'ftp';
?>


<div class="wrap">
	<h2> Filesystem Options </h2>
	<form name="filesystem" method="post">
	<strong>FTP:</strong> <input type="radio" name="filesystem[type]" value="ftp" onchange="$('#filesystem-ftp').show(); $('#filesystem-direct').hide();"<?php checked('ftp',$method); ?> /> &nbsp;
	<strong>Direct:</strong> <input type="radio" name="filesystem[type]" value="direct" onchange="$('#filesystem-direct').show(); $('#filesystem-ftp').hide();"<?php checked('direct',$method); ?> /><br />
	
	<div class="section" id="filesystem-ftp" style="<?php if('ftp' != $method) { echo 'display:none';} ?>">
	<script type="text/javascript">
		function filesystem_ftp_detect(){
			var ssl = 0;
			if( undefined != $('input[@name="filesystem[ftp][ssl]"]').attr('checked') )
				ssl = 1
			
			$.post('<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-update/wp-update-ajax.php?action=filesystem_get_ftp_path',
			  { hostname: $('input[@name="filesystem[ftp][hostname]"]').val(), 
			  	username: $('input[@name="filesystem[ftp][username]"]').val(), 
				password: $('input[@name="filesystem[ftp][password]"]').val(), 
				ssl: ssl,
				method: $('select[@name="filesystem[ftp][method]"]').val()  },
			  function(data){
			  	$('#ftp-status').html( data );
				//alert("JSON Data: " + data);
				$('input[@name="filesystem[ftp][basedir]"]').val( data.match(/Path: (.*?)</)[1].split(',') ); //Match Path: /blah/<br>
			  }
			);
		}
	</script>
		<?php $ftp = get_option('wpfs_ftp'); ?>
		<h3>FTP Options</h3>
		<strong>Hostname:</strong><input type="text" name="filesystem[ftp][hostname]" value="<?php echo attribute_escape($ftp['hostname']); ?>" autocomplete="off" /><br />
		<strong>Username:</strong><input type="text" name="filesystem[ftp][username]" value="<?php echo attribute_escape($ftp['username']); ?>" autocomplete="off" /><br />
		<strong>Password:</strong><input type="password" name="filesystem[ftp][password]" value="<?php if($ftp['password']){ echo "********";} ?>" autocomplete="off" />&nbsp;
								  <input type="checkbox" name="filesystem[ftp][passwordsave]" <?php checked(empty($ftp['password']),false); ?> />Save Password<br />
		<strong>Base Directory:</strong><input type="text" name="filesystem[ftp][basedir]" value="<?php echo attribute_escape($ftp['basedir']); ?>" autocomplete="off" />&nbsp;<input type="button" value="Automatically Detect" onclick="filesystem_ftp_detect();" /><br />
		
		<h3>Connection Options</h3>
		<input type="checkbox" name="filesystem[ftp][ssl]"<?php checked($ftp['ssl'],true); ?> /> Secure connection <em>(sFTP)</em><br />
		FTP Connection: <select name="filesystem[ftp][method]" >
							<option value="phpext"
								<?php if( !extension_loaded('ftp') ){ echo ' disabled="disabled"';} 
										selected('phpext',$ftp['method']); ?>>PHP FTP Extension</option>
							<option value="phpsocket"
								<?php if( !extension_loaded('sockets') ){ echo ' disabled="disabled"';} 
										selected('phpsocket',$ftp['method']); ?>>PHP Sockets</option>
							<option value="phpstream"
								<?php if( !function_exists('fsockopen') ){ echo ' disabled="disabled"';} 
										selected('phpstream',$ftp['method']); ?>>PHP Stream Sockets</option>
						</select>
		<div id="ftp-status">&nbsp;</div>
		
	</div>
	<div class="section" id="filesystem-direct" style="<?php if('direct' != $method) { echo 'display:none';} ?>">
		<h3>Direct Access Options</h3>
		<strong>Base Directory:</strong><input type="text" name="filesystem[direct][basedir]" id="fs-direct-base" value="" />&nbsp;
						<input type="button" value="Reset" onclick="$('#fs-direct-base').val('<?php echo attribute_escape(addslashes(ABSPATH)); ?>');" /><br />		
	</div>
	
	<p class="submit">
		<input type="submit" name="submit_filesystem" value="<?php _e('Save Filesystem Information &raquo;') ?>" />
	</p>
	</form>
</div>