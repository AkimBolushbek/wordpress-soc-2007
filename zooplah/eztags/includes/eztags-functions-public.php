<?php

require_once 'eztags-functions-private.php';

/**
 @desc Binds a function as a conversion routine
 @param string A function to bind
 @param array List of arguments to send to the function
*/
function eztags_bind($func, $param = NULL)
{
	global $_eztags_bind_array;
	$_eztags_bind_array[$func] = array($func, $param);
}

/**
 * Allows clean adding of a bound tag
 * @param string Raw tag to add
 * @return string Cleaned up tag
 */
function eztags_add($tag)
{
	return '?&gt;' . $tag . '&lt;?php';
}

?>
