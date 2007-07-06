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
		echo $wpupdate->getPluginUpdateText($_GET['file'],true,true,true);
		break;
	case 'themeSearch':
		$searchOptions['cats'] = explode(',',$_POST['cats']);
		$searchOptions['order'] = $_POST['order'];
		$searchOptions['sortby'] = $_POST['sortby'];
		$searchOptions['andor'] = $_POST['andor'];

		$paged = $_POST['paged'];
		if( empty($paged) || !is_numeric($paged) ) $paged = 1;
		
		$wpupdate = new WP_Update;
		$searchResults = $wpupdate->search('themes',$searchOptions,$paged);

		if( !isset($searchResults['results']) || empty($searchResults['results']) )
			die('no more results');
		foreach($searchResults['results'] as $theme)
			echo wpupdate_themeSearchHTML($theme);
		break;
}

?>