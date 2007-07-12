<?php

$in_loop = false;

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
	$arr = preg_split('/\n|\r|\r\n/', $content);
	$n_arr = count($arr);

	$content = '';

	for ($i = 0; $i < $n_arr; $i++)
	{
		$ct = $arr[$i];
		eztags_from_title($ct);
		$content .= "$ct\n";
	}
}

?>
