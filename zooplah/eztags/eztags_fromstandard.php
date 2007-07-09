<?php

$hide_php = get_option('hide_php');
$in_loop = false;

function normalize_loop_start(&$content)
{
	global $hide_php, $in_loop;

	if ( 'yes' == $hide_php )
	{
		//$content = preg_replace('/if\s*\(have_posts\(\)\)\s*:\s*while\s*\(have_posts\(\)\)\s*:\s*the_post\(\);/m', 'start_loop();', $content);
		$in_loop = true;
	}
}

function eztags_clean_markers(&$content)
{
	$content = preg_replace('/\)\s*\?&gt;/', '); ?&gt;', $content);

	$content = preg_replace('/&lt;\?php\s*\?&gt;/', '', $content);

	$content = preg_replace('/\?&gt;\s*&lt;\?php/m', "\n", $content);
}

function eztags_from_title(&$ct)
{
	preg_match('/the_title\(([^\)]*)\);?/', $ct, $matches);
	list($match, $attr) = $matches;

	$attrs = preg_split('/\,\s*/', $attr);
	$attrs = preg_replace('/\'|\"/', '', $attrs);
	list($before, $after) = $attrs;

	$ct = str_replace($match, "?&gt;$before<\$EntryTitle lang=\"en\"\$>$after&lt;?php", $ct);
}

function eztags_parse_std(&$content)
{
	eztags_clean_markers($content);
	normalize_loop_start($content);

	$arr = preg_split('/\n|\r|\r\n/', $content);
	$n_arr = count($arr);

	$content = '';

	for ($i = 0; $i < $n_arr; $i++)
	{
		$ct = $arr[$i];
		eztags_from_title($ct);
		$content .= "$ct\n";
	}

	eztags_clean_markers($content);
}

?>
