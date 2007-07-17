<?php
function WP_Filesystem($preference=false,$arg=''){
	if( !$preference )
		$preference = get_option('wpfs_method');
	$method = _WP_Filesystem_bestOption($preference);
	if( ! $method ) return;
	if( '' == $arg && strpos($method,'ftp') > -1 )
		$arg = get_option('wpfs_ftp');

	require('wp-update-filesystem-'.$method.'-class.php');
	$method = "WP_Filesystem_$method";
	return new $method($arg);
}
function _WP_Filesystem_bestOption($preference='direct'){
	switch($preference){
		default:
		case 'direct':
			//Likely suPHP or windows.
			if( getmyuid() == fileowner(tempnam("/tmp", "FOO")) )
				return 'direct';
			break;
		case 'ftpext':
			if( extension_loaded('ftp') )
				return 'ftpext';
			break;
		case 'ftpsocket':
			if( function_exists('socket_create') )
				return 'ftpsocket';
			break;
	}
	if( getmyuid() == fileowner(tempnam("/tmp", "FOO")) ) return 'direct';
	if( extension_loaded('ftp')) return 'ftpext';
	if( function_exists('socket_create') ) return 'ftpsocket';
	return false;
}
?>