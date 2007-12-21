<?php

/* Get the relative path where the plugin is installed */
function get_renard_dir()
{
	preg_match('/\/wp-content\/plugins\/(.*)$/', htmlspecialchars(__FILE__), $matches);
	list($match, $relative_file) = $matches;
	$renard_dir = str_replace('/renard/includes', '', dirname($relative_file));

	if ( $renard_dir )
		$renard_dir .= '/';

	return $renard_dir;
}

/* Set up stuff for internationalization */
$renard_domain = 'renard';
load_plugin_textdomain($renard_domain, 'wp-content/plugins/' . get_renard_dir() . 'renard/languages');

function _ez($str)
{
	global $renard_domain;
	_e($str, $renard_domain);
}

function __z($str)
{
	global $renard_domain;
	return __($str, $renard_domain);
}

/* Set up for binding custom tags */
$_renard_bind_array = array();

function renard_wind(&$content)
{
	global $_renard_bind_array;

	foreach ($_renard_bind_array as $binded)
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
			call_user_func($func, &$content);
	}
}

?>
