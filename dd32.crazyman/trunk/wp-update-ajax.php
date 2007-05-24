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
		echo $wpupdate->checkPluginUpdate($_GET['file']);
		break;
}

?>