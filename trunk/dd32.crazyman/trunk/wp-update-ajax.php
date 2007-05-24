<?php
require_once('../../../wp-config.php');
require_once('wp-update.php');
require_once('../../../wp-admin/admin.php');
include_once('includes/wp-update-class.php');

//Function returns rawa data from plugin.

switch($_GET['action']){
	case 'checkPluginUpdate':
		echo $_GET['file'];
		//$wpupdate = new WP_Update;
		//return $wpupdate->checkPluginUpdate($_GET['file']);
		break;
}

?>
BOO