<?php

/* Converts standard tags to easy tags.
 * Contains functions for the conversion; format:
 * function eztags_from_tag_name(&$ct)
 */

function eztags_from_author(&$ct)
{
	$ct = preg_replace('/the_author\(\s*\);?/', '?&gt;<$EntryAuthor$>&lt;?php', $ct);
}

function eztags_from_author_posts(&$ct)
{
	$ct = preg_replace('/the_author_posts_link\(\s*\);?/', '?&gt;<$EntryAuthorPostsLink$>&lt;?php', $ct);
}

function eztags_from_blog_info(&$ct)
{
	$ct = preg_replace('/bloginfo\(([^\)]+)\);?/', '?&gt;<$WPInfo:$1$>&lt;?php', $ct);
	$ct = preg_replace('/<\$WPInfo:\'([^\']+)\'\$>/', '<$WPInfo:$1$>', $ct);
}

function eztags_from_calendar(&$ct)
{
	preg_match('/get_calendar\(([^\)]*)\);?/', $ct, $matches);
	list($match, $arg) = $matches;

	if ($arg == 'false')
		$ct = str_replace($match, '?&gt;<$WPCalendar3$>&lt;?php', $ct);
	else
		$ct = str_replace($match, '?&gt;<$WPCalendar$>&lt;?php', $ct);
}

function eztags_from_category(&$ct)
{
	$ct = preg_replace('/the_category\(\'?([^\']*)\'?\);?/', '?&gt;<EntryCategories>$1</EntryCategories>&lt;?php', $ct);
}

function eztags_from_comment_author(&$ct)
{
	$ct = preg_replace('/comment_author\(\s*\);?/', '?&gt;<$CommentAuthor$>&lt;?php', $ct);
}

function eztags_from_comment_author_link(&$ct)
{
	$ct = preg_replace('/comment_author_link\(\s*\);?/', '?&gt;<$CommentAuthorLink$>&lt;?php', $ct);
}

function eztags_from_comment_author_url(&$ct)
{
	$ct = preg_replace('/comment_author_url\(\s*\);?/', '?&gt;<$CommentAuthorURL$>&lt;?php', $ct);
}

function eztags_from_comment_date(&$ct)
{
	$ct = preg_replace('/comment_date\(\);?/', '?&gt;<$CommentDate$>&lt;?php', $ct);
	$ct = preg_replace('/comment_date\(\'([^\']+)\'\);?/', '?&gt;<$CommentDate:$1$>&lt;?php', $ct);
}

function eztags_from_comment_id(&$ct)
{
	$ct = preg_replace('/comment_ID\(\s*\);?/', '?&gt;<$CommentID$>&lt;?php', $ct);
}

function eztags_from_comment_text(&$ct)
{
	$ct = preg_replace('/comment_text\(\s*\);?/', '?&gt;<$CommentText$>&lt;?php', $ct);
}

function eztags_from_comment_time(&$ct)
{
	$ct = preg_replace('/comment_time\(\);?/', '?&gt;<$CommentTime$>&lt;?php', $ct);
}

function eztags_from_content(&$ct)
{
	$in_ct = $ct;

	$ct = preg_replace('/the_content\(__\((.*)\)\);/', '?&gt;<EntryContent>$1</EntryContent>&lt;?php', $ct);
	$ct = preg_replace('/the_content\(\'([^\']*)\'\);?/', '?&gt;<EntryContent>$1</EntryContent>&lt;?php', $ct);
	$ct = preg_replace('/the_content\(\);?/', '?&gt;<EntryContent></EntryContent>&lt;?php', $ct);

	if ( $ct !== $in_ct )
		$ct = str_replace("'", '', $ct);
}

function eztags_from_date(&$ct)
{
	preg_match('/the_date\(([^\)]*)\);?/', $ct, $matches);
	list($match, $attr) = $matches;

	$attrs = preg_split('/\,\s*/', $attr);
	$attrs = preg_replace('/\'/', '', $attrs);
	$attrs = preg_replace('/&quot;/', '', $attrs);
	list($format, $before, $after) = $attrs;

	if ( !$format )
		$ct = str_replace($match, "?&gt;$before<\$EntryDate\$>$after&lt;?php", $ct);
}

function eztags_from_e(&$ct)
{
	preg_match('/_e\(([^\);]+)\);/', $ct, $matches);
	list($match, $content) = $matches;
	$content = preg_replace('/\'/', '', $content);
	$content = preg_replace('/&quot;/', '', $content);

	$ct = str_replace($match, "?&gt;<TranslatableString>$content</TranslatableString>&lt;?php", $ct);
}

function eztags_from_excerpt(&$ct)
{
	$ct = preg_replace('/the_excerpt\(\s*\);?/', '?&gt;<$EntryExcerpt$>&lt;?php', $ct);
}

function eztags_from_get_archives(&$ct)
{
	$ct = preg_replace('/wp_get_archives\(\'?([^\']*)\'?\);?/', '?&gt;<$WPArchives:$1$>&lt;?php', $ct);
}

function eztags_from_id(&$ct)
{
	$ct = preg_replace('/the_ID\(\s*\);?/', '?&gt;<$EntryID$>&lt;?php', $ct);
}

function eztags_from_language_attributes(&$ct)
{
	$ct = preg_replace('/language_attributes\(\s*\);?/', '?&gt;<$WPLanguageAttributes$>&lt;?php', $ct);
}

function eztags_from_link_pages(&$ct)
{
	$ct = preg_replace('/wp_link_pages\(\'?([^\']*)\'?\);?/', '?&gt;<$WPLinkPages:$1$>&lt;?php', $ct);
}

function eztags_from_links_list(&$ct)
{
	$ct = preg_replace('/get_links_list\(\'?([^\']*)\'?\);?/', '?&gt;<$WPLinks:$1$>&lt;?php', $ct);
	$ct = str_replace('<$WPLinks:$>', '<$WPLinks:name$>', $ct);
}

function eztags_from_list_bookmarks(&$ct)
{
	$ct = preg_replace('/wp_list_bookmarks\(\'?([^\']*)\'?\);?/', '?&gt;<$WPBookmarks:$1$>&lt;?php', $ct);
}

function eztags_from_list_categories(&$ct)
{
	$ct = preg_replace('/wp_list_categories\(\'?([^\']*)\'?\);?/', '?&gt;<$WPCategories:$1$>&lt;?php', $ct);
}

function eztags_from_list_cats(&$ct)
{
	$ct = preg_replace('/wp_list_cats\(\'?([^\']*)\'?\);?/', '?&gt;<$WPCategoriesOld:$1$>&lt;?php', $ct);
}

function eztags_from_list_pages(&$ct)
{
	$ct = preg_replace('/wp_list_pages\(\s*\'?([^\']*)\'?\s*\);?/', '?&gt;<$WPPages:$1$>&lt;?php', $ct);
}

function eztags_from_login(&$ct)
{
	$ct = preg_replace('/wp_loginout\(\s*\);?/', '?&gt;<$WPLoginOut$>&lt;?php', $ct);
}

function eztags_from_meta(&$ct)
{
	$ct = preg_replace('/wp_meta\(\s*\);?/', '?&gt;<$WPMeta$>&lt;?php', $ct);
}

function eztags_from_permalink(&$ct)
{
	$ct = preg_replace('/the_permalink\(\s*\);?/', '?&gt;<$EntryPermalink$>&lt;?php', $ct);
}

function eztags_from_register(&$ct)
{
	preg_match('/wp_register\(([^\)]*)\);?/', $ct, $matches);
	list($match, $attr) = $matches;

	$attrs = preg_split('/\,\s*/', $attr);
	$attrs = preg_replace('/\'/', '', $attrs);
	$attrs = preg_replace('/&quot;/', '', $attrs);
	list($before, $after) = $attrs;

	$ct = str_replace($match, "?&gt;$before<\$WPRegister\$>$after&lt;?php", $ct);
}

function eztags_from_search_query(&$ct)
{
	$ct = preg_replace('/the_search_query\(\s*\);?/', '?&gt;<$WPSearch$>&lt;?php', $ct);
}

function eztags_from_single_cat_title(&$ct)
{
	$ct = preg_replace('/single_cat_title\(\'?([^\']*)\'?\);?/', '?&gt;<CurrentCategory>$1</CurrentCategory>&lt;?php', $ct);
}

function eztags_from_time(&$ct)
{
	$ct = preg_replace('/the_time\(\s*\);?/', '?&gt;<$EntryTime$>&lt;?php', $ct);
	$ct = preg_replace('/the_time\(\'([^\']+)\'\);?/', '?&gt;<$EntryTime:$1$>&lt;?php', $ct);
}

function eztags_from_title(&$ct)
{
	preg_match('/the_title\(([^\)]*)\);?/', $ct, $matches);
	list($match, $attr) = $matches;

	$attrs = preg_split('/\,\s*/', $attr);
	$attrs = preg_replace('/\'/', '', $attrs);
	$attrs = preg_replace('/&quot;/', '', $attrs);
	list($before, $after) = $attrs;

	$ct = str_replace($match, "?&gt;$before<\$EntryTitle\$>$after&lt;?php", $ct);
}

function eztags_from_trackback_url(&$ct)
{
	preg_replace('/trackback_url\((true)?\);?/i', '?&gt;<$EntryTrackbackURL$>&lt;?php', $ct);
}

function eztags_parse_from(&$ct)
{
	eztags_from_author($ct);
	eztags_from_author_posts($ct);
	eztags_from_blog_info($ct);
	eztags_from_calendar($ct);
	eztags_from_category($ct);
	eztags_from_comment_author($ct);
	eztags_from_comment_author_link($ct);
	eztags_from_comment_author_url($ct);
	eztags_from_comment_id($ct);
	eztags_from_comment_date($ct);
	eztags_from_comment_text($ct);
	eztags_from_comment_time($ct);
	eztags_from_content($ct);
	eztags_from_date($ct);
	eztags_from_e($ct);
	eztags_from_excerpt($ct);
	eztags_from_get_archives($ct);
	eztags_from_id($ct);
	eztags_from_language_attributes($ct);
	eztags_from_link_pages($ct);
	eztags_from_links_list($ct);
	eztags_from_list_bookmarks($ct);
	eztags_from_list_categories($ct);
	eztags_from_list_cats($ct);
	eztags_from_list_pages($ct);
	eztags_from_login($ct);
	eztags_from_meta($ct);
	eztags_from_permalink($ct);
	eztags_from_register($ct);
	eztags_from_search_query($ct);
	eztags_from_single_cat_title($ct);
	eztags_from_time($ct);
	eztags_from_title($ct);
	eztags_from_trackback_url($ct);
}

function eztags_parse_std(&$content)
{
	$arr = preg_split('/[\n\r]{1,2}/', $content);
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
