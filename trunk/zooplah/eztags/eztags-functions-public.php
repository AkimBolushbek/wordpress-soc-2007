<?php

function eztags_bind($func, $param = array())
{
	global $_eztags_bind_array;
	$_eztags_bind_array[$func] = array($func, $param);
}

?>
