<?php
function WP_Filesystem_check($preference=false,$arg=false){
	return WP_Filesystem($preference,$arg,'check');
}
function WP_Filesystem($preference=false,$arg=false,$action='connect'){
	if( ! $preference )
		$preference = get_option('wpfs_method');

	$method = _WP_Filesystem_bestOption($preference);
	if( ! $method ) return;
	if( strpos($method,'ftp') > -1 )
		$arg = array_merge((array)get_option('wpfs_ftp'), (array)$arg);

	require_once('wp-update-filesystem-'.$method.'-class.php');
	$method = "WP_Filesystem_$method";
	$fs = new $method($arg);
	if( 'check' == $action ){
		$ret = $fs->errors;
		unset($fs);
		return !empty($ret) ? $ret : true;
	}
	if ('connect' == $action){
		$numerrors = count($fs->errors);
		$fs->connect();
		if( $numerrors != count($fs->errors) ){
			//There was an erorr connecting to the server.
			$ret = $fs->errors;
			unset($fs);
			return $ret;
		}
	}
	return $fs;
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