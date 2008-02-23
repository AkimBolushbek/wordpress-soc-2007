<?php

require_once 'renard-functions-private.php';

/**
 @desc Binds a function as a conversion routine
 @param string A function to bind
 @param array List of arguments to send to the function
*/
function renard_bind($func, $param = NULL)
{
	global $_renard_bind_array;
	$_renard_bind_array[$func] = array($func, $param);
}

/**
 @desc Binds a function as a reverse conversion routine
 @param string A function to bind
 @param array List of arguments to send to the function
*/
function renard_unbind($func, $param = NULL)
{
	global $_renard_unbind_array;
	$_renard_unbind_array[$func] = array($func, $param);
}

/**
 * Allows clean adding of a bound tag
 * @param string Raw tag to add
 * @return string Cleaned up tag
 */
function renard_add($tag)
{
	return '?&gt;' . $tag . '&lt;?php';
}

?>
