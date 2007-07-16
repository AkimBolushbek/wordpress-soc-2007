<?php

/* Converts standard tags to easy tags.
 * Contains functions for the conversion; format:
 * function eztags_from_tag_name(&$ct)
 */

function eztags_from_author(&$ct)
{
	$ct = preg_replace('/the_author\(\s*\);?/', '?&gt;<$EntryAuthor$>&lt;?php', $ct);
}

function eztags_from_comment_id(&$ct)
{
	$ct = preg_replace('/comment_ID\(\s*\);?/', '?&gt;<$CommentID$>&lt;?php', $ct);
}

function eztags_from_e(&$ct)
{
	preg_match('/_e\((.+)\)\;?/', $ct, $matches);
	list($match, $content) = $matches;
	$content = preg_replace('/\'|&quot;/', '', $content);

	$ct = str_replace($match, "?&gt;<TranslatableString>$content</TranslatableString>&lt;?php", $ct);
}

function eztags_from_id(&$ct)
{
	$ct = preg_replace('/the_ID\(\s*\);?/', '?&gt;<$EntryID$>&lt;?php', $ct);
}

function eztags_from_permalink(&$ct)
{
	$ct = preg_replace('/the_permalink\(\s*\);?/', '?&gt;<$EntryPermalink$>&lt;?php', $ct);
}

function eztags_from_title(&$ct)
{
	preg_match('/the_title\(([^\)]*)\);?/', $ct, $matches);
	list($match, $attr) = $matches;

	$attrs = preg_split('/\,\s*/', $attr);
	$attrs = preg_replace('/\'|\&quot;/', '', $attrs);
	list($before, $after) = $attrs;

	$ct = str_replace($match, "?&gt;$before<\$EntryTitle lang=\"en\"\$>$after&lt;?php", $ct);
}

function eztags_parse_from(&$ct)
{
	eztags_from_author($ct);
	eztags_from_comment_id($ct);
	eztags_from_e($ct);
	eztags_from_id($ct);
	eztags_from_permalink($ct);
	eztags_from_title($ct);
}

function eztags_parse_std(&$content)
{
	$arr = preg_split('/\n|\r|\r\n/', $content);
	$n_arr = count($arr);

	$content = '';

	for ($i = 0; $i < $n_arr; $i++)
	{
		$ct = $arr[$i];
		eztags_parse_from($ct);
		$content .= "$ct\n";
	}
}

?>
