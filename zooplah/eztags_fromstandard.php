<?php

$hide_php = get_option('hide_php');
$in_loop = false;

function normalize_loop_start(&$content)
{
	global $hide_php, $in_loop;

	if ( 'yes' == $hide_php )
	{
		$content = preg_replace('/if\s*\(have_posts\(\)\)\s*:\s*while\s*\(have_posts\(\)\)\s*:\s*the_post\(\);/m', 'start_loop();', $content);
		$in_loop = true;
	}
}

function eztags_parse_std(&$content)
{
	normalize_loop_start($content);

	$arr = preg_split('/\n|\r|\r\n/', $content);
	$n_arr = count($arr);

	$content = '';

	for ($i = 0; $i < $n_arr; $i++)
	{
		preg_match('/([\w|\d|_]+)\([^\)(;|\s)]*\)[;|\s+]/', $arr[$i], $matches);
		list($match, $elem, $params) = $matches;
		echo "Element: $elem; Paremeter list: $params;\n Original line: $arr[$i]\n\n";
	}
}


?>
