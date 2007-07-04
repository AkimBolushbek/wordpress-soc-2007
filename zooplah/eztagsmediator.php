<?php

/* Mediates between the two types of template tags */

require_once 'eztags_fromstandard.php';
require_once 'eztags_tostandard.php';

/* Replace Standard tags with Easy tags */
function std2ez($content)
{
	eztags_parse_std($content);

	return $content;
}

/* Replace Easy tags with Standard tags */
function ez2std($content)
{
	return $content;
}
