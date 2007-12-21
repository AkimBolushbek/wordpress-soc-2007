<?php

/* Mediates between the two types of template tags */

require_once 'renard-from-standard.php';
require_once 'renard-to-standard.php';

function remove_empty_php(&$content)
{
	$content = preg_replace('/&lt;\?php\s*\?&gt;/', '', $content);
}

function remove_extra_lines(&$content)
{
	$content = preg_replace('/[\n\r]{2,}/m', "\n\n", $content);
}

/* Replace Standard tags with Easy tags */
function std2ez($content)
{
	renard_parse_std($content);
	remove_empty_php($content);
	remove_extra_lines($content);

	return $content;
}

/* Replace Easy tags with Standard tags */
function ez2std($content)
{
	renard_parse_ez($content);
	// Quitting and then instantly restarting PHP mode can cause
	// problems
	$content = preg_replace('/(\s*)\?><\?php\s*/', '$1', $content);

	// Localized empty string is not the same as an empty
	// string
	$content = str_replace("__('')", '', $content);

	return $content;
}

?>
