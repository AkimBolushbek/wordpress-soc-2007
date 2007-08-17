<?php

require_once 'includes/eztags-functions-private.php';

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

?>
