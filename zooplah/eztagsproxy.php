<?php

/* Converts back and forth between the two types of template tags */

require_once 'eztags_fromstandard.php';
require_once 'eztags_tostandard.php';

/* Replace Standard tags with Easy tags */
function std2ez($content)
{
	stripPHP($content);

	/* The Actual tags */
	$content = str_replace('the_title();', '<$EntryTitle$>', $content);

	return $content;
}

/* Replace Easy tags with Standard tags */
function ez2std($content)
{
	insertPreservers($content);

	$content = str_replace('<$EntryTitle$>', '<?php the_title(); ?>', $content);

	return $content;
}
