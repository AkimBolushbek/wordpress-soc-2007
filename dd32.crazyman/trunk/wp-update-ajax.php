<?php
require_once('../../../wp-config.php');
require_once('../../../wp-admin/admin.php');
include_once('includes/wp-update-class.php');
include_once('includes/wp-update-functions.php');

$wpupdate = new WP_Update;

switch($_GET['action']){
	case 'checkPluginUpdate':
		echo $wpupdate->getPluginUpdateText($_GET['file'],true,true,true);
		break;
	case 'themeSearch':
		$searchOptions['cats'] = explode(',',$_POST['cats']);
		$searchOptions['order'] = $_POST['order'];
		$searchOptions['sortby'] = $_POST['sortby'];
		$searchOptions['andor'] = $_POST['andor'];

		$paged = $_POST['paged'];
		if( empty($paged) || !is_numeric($paged) ) $paged = 1;
		
		$searchResults = $wpupdate->search('themes',$searchOptions,$paged);

		if( !isset($searchResults['results']) || empty($searchResults['results']) )
			die('no more results');
		foreach($searchResults['results'] as $theme)
			echo wpupdate_themeSearchHTML($theme);
		break;
	case 'pluginSearch':
		$page = (int)$_POST['page'];
		if( isset($_POST['tag']) ){
			$results = $wpupdate->getPluginsByTag($_POST['tag'],$page);
		} elseif ( isset($_POST['term']) ) {
			$results = $wpupdate->search('plugins',$_POST['term'],$page);
		}
		if( !isset($results['results']) || empty($results['results']) )
			echo __('no more results');
		else 
			foreach($results['results'] as $plugin)
				echo wpupdate_pluginSearchHTML($plugin);
		break;
	case 'filesystem_get_ftp_path':
		include_once('includes/wp-update-filesystem-class.php');
		$fs = WP_Filesystem($_POST['method'],$_POST);
		echo 'Path: ' . $fs->find_base_dir('.',true) . '<br/>';
		break;
}
?>