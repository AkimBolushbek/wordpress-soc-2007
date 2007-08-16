<?php

/* Get the relative path where the plugin is installed */
function get_eztags_dir()
{
	preg_match('/\/wp-content\/plugins\/(.*)$/', htmlspecialchars(__FILE__), $matches);
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

/* Set up stuff for internationalization */
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

/* Set up for binding custom tags */
$_eztags_bind_array = array();

function eztags_wind(&$content)
{
	global $_eztags_bind_array;

	foreach ($_eztags_bind_array as $binded)
	{
		list($func, $param) = $binded;

		if ( NULL !== $param )
		{
			if ( !is_array($param) )
				$param = array($param);

			array_unshift($param, &$content);
			call_user_func_array($func, $param);
		}
		else
			call_user_func($func, $content);
	}
}

?>
