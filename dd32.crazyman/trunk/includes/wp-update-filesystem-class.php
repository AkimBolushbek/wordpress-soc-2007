<?php
function WP_Filesystem($preference='',$arg=''){
	$method = _WP_Filesystem_bestOption($method);
	if( ! $method ) return;

	require('wp-update-filesystem-'.$method.'-class.php');
	$method = "WP_Filesystem_$method";
	return new $method($arg);
}
function _WP_Filesystem_bestOption($preference='direct'){
	//No Breaks here, we want to go through each item, Starting from the preferential item.
	switch($preference){
		default:
		case 'direct':
			//Likely suPHP or windows.
			if( getmyuid() == fileowner(__FILE__) ) return 'direct';
		case 'ftp':
			if( extension_loaded('ftp') ) return 'ftp';
		case 'ftpsocket':
			if( function_exists('socket_create') ) return 'ftpsocket';
	}
	if( getmyuid() == fileowner(__FILE__) ) return 'direct';
	if( extension_loaded('ftp')) return 'ftp';
	if( function_exists('socket_create') ) return 'ftpsocket';
	return false;
}
?>