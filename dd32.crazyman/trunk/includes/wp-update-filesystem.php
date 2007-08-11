<?php
function WP_Filesystem_check($preference=false,$arg=false){
	global $wp_filesystem;
	if( ! $wp_filesystem )
		WP_Filesystem($preference,$arg);
	return empty($wp_filesystem->errors);
}
function WP_Filesystem($preference=false,$arg=false){
	global $wp_filesystem;
	if( ! $preference )
		$preference = get_option('wpfs_method');

	$method = _WP_Filesystem_bestOption($preference);
	if( ! $method )
		return false;
	if( strpos($method,'ftp') > -1 )
		$arg = array_merge((array)get_option('wpfs_ftp'), (array)$arg);

	require_once('wp-update-filesystem-'.$method.'-class.php');
	$method = "WP_Filesystem_$method";

	$wp_filesystem = new $method($arg);

	$numerrors = count($wp_filesystem->errors);
	$wp_filesystem->connect();
	if( $numerrors != count($wp_filesystem->errors) )
		return false; //There was an erorr connecting to the server.
	return true;
}
function _WP_Filesystem_bestOption($preference='direct'){
	switch($preference){
		default:
		case 'direct':
			//Likely suPHP or windows.
			if( getmyuid() == fileowner(tempnam("/tmp", "FOO")) )
				return 'direct';
			break;
		case 'phpext':
			if( extension_loaded('ftp') )
				return 'ftpext';
			break;
		case 'phpsocket':
			if( extension_loaded('sockets') )
				return 'ftpsocket';
			break;
		case 'phpstream':
			break;
	}
	if( getmyuid() == fileowner(tempnam('/tmp', 'WPU')) ) return 'direct';
	if( extension_loaded('ftp') ) return 'ftpext';
	if( extension_loaded('sockets') ) return 'ftpsocket';
	return false;
}
?>
