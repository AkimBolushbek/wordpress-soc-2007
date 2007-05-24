<?php
require_once('../../../wp-config.php');
require_once('wp-update.php');
require_once('../../../wp-admin/admin.php');
include_once('includes/wp-update-class.php');
include_once('includes/wp-update-functions.php');

//Function returns raw data from plugin.

switch($_GET['action']){
	case 'checkPluginUpdate':
		$wpupdate = new WP_Update;
		$updateStat = $wpupdate->checkPluginUpdate($_GET['file'],true,true);
		//TODO: Seems to be firing for disabled plugins regardless
		if( isset($updateStat['Errors']) && in_array('Not Found',$updateStat['Errors']) ){
			$updatetext = '';
			foreach($updateStat['Errors'] as $error)
				$updatetext .= __($error)."<br/>";
		} elseif( ( isset($updateStat['Errors']) && in_array('Not Cached',$updateStat['Errors']) )
			|| (get_option('update_check_inactive') && !in_array($plugin_file, $current_plugins)  ) ){
			//Plugin info not cached.
			$updatetext = __('Please Wait');
			$updatetext .= "<script type='text/javascript'>checkUpdate('$plugin_file');</script>";
		} elseif ( !isset($updateStat['Update']) && !get_option('update_check_inactive') && !in_array($plugin_file, $current_plugins) ){
			$updatetext = __('Not Checked');
		} else {
			//Update is available; display it.
			if( $updateStat['Update'] ){
				$updatetext = __('Update Available').':<br/>';
				$updatetext .= $updateStat['Version'];
				$updatetext .= '<br/><a href="'.$updateStat['PluginInfo']['Download'].'">'.__('Install').'</a>';
				if( isset($updateStat['Errors']) ){
					$updatetext .= '<br/><span class="updateerror">';
					$updatetext .= implode('<br/>',$updateStat['Errors']);
					$updatetext .= '</span>';
				}
			} else {
				$updatetext = __('Latest Installed');
			} //updatestat
		} //isset()
		echo $updatetext;
		break;
}

?>