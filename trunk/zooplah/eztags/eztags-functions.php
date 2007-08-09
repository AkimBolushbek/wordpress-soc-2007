<?php

function get_eztags_dir()
{
	preg_match('/\/wp-content\/plugins\/(.*)$/', __FILE__, $matches);
	list($match, $relative_file) = $matches;
	$eztags_dir = dirname($relative_file);

	if ( '.' != $eztags_dir )
		$eztags_dir .= '/';
	/* $eztags_dir is only a point;
	 * that is, not in a subdirectory */
	else
		$eztags_dir = '';

	return $eztags_dir;
}

$eztags_domain = 'eztags';
load_plugin_textdomain($eztags_domain, 'wp-content/plugins/' . get_eztags_dir() . 'languages');

function _ez($str)
{
	global $eztags_domain;
	_e($str, $eztags_domain);
}

function _z($str)
{
	global $eztags_domain;
	return __($str, $eztags_domain);
}

?>
