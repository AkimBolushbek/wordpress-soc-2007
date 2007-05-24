<?php
require_once('../../../wp-config.php');
require_once('wp-update.php');
require_once('../../../wp-admin/admin.php');
include_once('includes/wp-update-class.php');
include_once('includes/wp-update-functions.php');

//Function returns rawa data from plugin.

switch($_GET['action']){
	case 'checkPluginUpdate':
		$wpupdate = new WP_Update;
		$updateStat = $wpupdate->checkPluginUpdate($_GET['file'],true,true);
		
		if( ! isset($updateStat['Update']) ){
			_e('Not Available');
		} else {
			if( $updateStat['Update'] ){
				_e('Update Available');
				echo ':<br/>'.$updateStat['Version'];
				echo ' <a href="#">';
				_e('Install');
				echo '</a>';
				if( isset($updateStat['Errors']) ){
					echo '<br/><span class="updateerror">';
					echo implode('<br/>',$updateStat['Errors']);
					echo '</span>';
				}
			} else {
				_e('Latest Installed');
			}
		}
		break;
}

?>