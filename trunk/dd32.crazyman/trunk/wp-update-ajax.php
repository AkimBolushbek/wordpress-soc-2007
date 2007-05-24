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
		$status = $wpupdate->checkPluginUpdate($_GET['file']);
		if( null === $status ) {
			echo 'Not Available';
		} elseif ( false === $status){
			echo 'Latest Installed';
		} else {
			echo 'New Version: '.$status.'<br><a href="#">Install</a>';
		}
		break;
}

?>