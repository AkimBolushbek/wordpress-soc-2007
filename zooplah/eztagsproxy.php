<?php

/* Converts back and forth between the two types of template tags */

/* Replace Standard tags with Easy tags */
function std2ez($content)
{
	/* Get rid of the PHP start and end markers */
	$content = str_replace('&lt;?php', '', $content);
	$content = str_replace('?&gt;', '', $content);

	/* The Actual tags */
	$content = str_replace('the_title();', '<$EntryTitle$>', $content);

	return $content;
}

/* Replace Easy tags with Standard tags */
function ez2std(&$content)
{
	$content = str_replace('<$EntryTitle', 'the_title()', $content);

	return $content;
}
