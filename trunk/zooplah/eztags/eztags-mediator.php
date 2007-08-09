<?php

/* Mediates between the two types of template tags */

require_once 'eztags-from-standard.php';
require_once 'eztags-to-standard.php';

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
	eztags_parse_std($content);
	remove_empty_php($content);
	remove_extra_lines($content);

	return $content;
}

/* Replace Easy tags with Standard tags */
function ez2std($content)
{
	eztags_parse_ez($content);
	// Quitting and then instantly restarting PHP mode con cause
	// problems
	$content = preg_replace('/\s+\?><\?php\s*/', ' ', $content);

	return $content;
}

?>
