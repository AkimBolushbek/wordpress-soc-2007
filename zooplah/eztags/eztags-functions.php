<?php

function get_eztags_dir()
{
	preg_match('/\/wp-content\/plugins\/(.*)$/', __FILE__, $matches);
	list($match, $relative_file) = $matches;
	$eztags_dir = dirname($relative_file);

	if ( '.' != $eztags_dir )
		$eztags_dir .= '/';
	/* $eztags_dir is only a point;
	 * that is, not in a subdirectory */
	else
		$eztags_dir = '';

	return $eztags_dir;
}

?>
