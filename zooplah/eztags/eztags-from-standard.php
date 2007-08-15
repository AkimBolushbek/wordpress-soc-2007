<?php

$_eztags_in_ifentries = FALSE;

/* Converts standard tags to easy tags.
 * Contains functions for the conversion; format:
 * function eztags_from_tag_name(&$content)
 */

function eztags_from_author(&$content)
{
	$content = preg_replace('/([^_])the_author\(\s*\);?/', '$1?&gt;<$EntryAuthor$>&lt;?php', $content);
}

function eztags_from_author_link(&$content)
{
	$content = preg_replace('/the_author_link\(\s*\);?/', '?&gt;<$EntryAuthorLink$>&lt;?php', $content);
}

function eztags_from_author_posts(&$content)
{
	$content = preg_replace('/the_author_posts_link\(\s*\);?/', '?&gt;<$EntryAuthorPostsLink$>&lt;?php', $content);
}

function eztags_from_blog_info(&$content)
{
	$content = preg_replace('/([^_])bloginfo\(([^\)]+)\);?/', '$1?&gt;<$WPInfo:$2$>&lt;?php', $content);
	$content = preg_replace('/<\$WPInfo:\'([^\']+)\'\$>/', '<$WPInfo:$1$>', $content);
}

function eztags_from_calendar(&$content)
{
	preg_match('/get_calendar\(([^\)]*)\);?/', $content, $matches);
	list($match, $arg) = $matches;

	if ($arg == 'false')
		$content = str_replace($match, '?&gt;<$WPCalendar3$>&lt;?php', $content);
	else
		$content = str_replace($match, '?&gt;<$WPCalendar$>&lt;?php', $content);
}

function eztags_from_category(&$content)
{
	$content = preg_replace('/the_category\(\'?([^\']*)\'?\);?/', '?&gt;<EntryCategories>$1</EntryCategories>&lt;?php', $content);
}

function eztags_from_comment_author(&$content)
{
	$content = preg_replace('/comment_author\(\s*\);?/', '?&gt;<$CommentAuthor$>&lt;?php', $content);
}

function eztags_from_comment_author_link(&$content)
{
	$content = preg_replace('/comment_author_link\(\s*\);?/', '?&gt;<$CommentAuthorLink$>&lt;?php', $content);
}

function eztags_from_comment_author_url(&$content)
{
	$content = preg_replace('/comment_author_url\(\s*\);?/', '?&gt;<$CommentAuthorURL$>&lt;?php', $content);
}

function eztags_from_comment_date(&$content)
{
	$content = preg_replace('/([^_])comment_date\(\);?/', '$1?&gt;<$CommentDate$>&lt;?php', $content);
	$content = preg_replace('/([^_])comment_date\(\'([^\']+)\'\);?/', '$1?&gt;<$CommentDate:$2$>&lt;?php', $content);
}

function eztags_from_comment_id(&$content)
{
	$content = preg_replace('/([^_])comment_ID\(\s*\);?/', '$1?&gt;<$CommentID$>&lt;?php', $content);
}

function eztags_from_comment_text(&$content)
{
	$content = preg_replace('/comment_text\(\s*\);?/', '?&gt;<$CommentText$>&lt;?php', $content);
}

function eztags_from_comment_time(&$content)
{
	$content = preg_replace('/([^_])comment_time\(\);?/', '$1?&gt;<$CommentTime$>&lt;?php', $content);
}

function eztags_from_comments(&$content)
{
	$content = preg_replace('/comments_template\(\s*\);?/', '?&gt;<$WPLoadComments$>&lt;?php', $content);
}

function eztags_from_comments_number(&$content)
{
	preg_match('/comments_number\(([^\)]*)\);?/', $content, $matches);

	list($match, $params) = $matches;
	$params = str_replace("'", '', $params);

	list($zero, $one, $more, $number) = preg_split('/\s*\,\s*/', $params);

	$content = str_replace($match, "?&gt;<\$EntryCommentsNumber zero=\"$zero\" one=\"$one\" more=\"$more\" number=\"$number\"\$>&lt;?php", $content);
}

function eztags_from_comments_open(&$content)
{
	$content = preg_replace('/if\s*\(\s*comments_open\(\s*\)\s*\)\s*:/', '?&gt;<$WPIfCommentsOpen$>&lt;?php', $content);
}

function eztags_from_comments_rss_link(&$content)
{
	preg_match('/comments_rss_link\(([^\)]*)\);?/', $content, $matches);
	list($match, $attrs) = $matches;

	list($text, $file) = preg_split('/\s*,\s*/', $attrs);

	$content = str_replace($match, "?&gt;<CommentsRSSLink>$text</CommentsRSSLink>&lt;?php", $content);
}

function eztags_from_content(&$content)
{
	$in_ct = $content;

	$content = preg_replace('/the_content\(([^\)]+)\);?/', '?&gt;<EntryContent>$1</EntryContent>&lt;?php', $content);
	$content = preg_replace('/the_content\(\'([^\']*)\'\);?/', '?&gt;<EntryContent>$1</EntryContent>&lt;?php', $content);
	$content = preg_replace('/the_content\(\);?/', '?&gt;<EntryContent></EntryContent>&lt;?php', $content);

	if ( $content !== $in_ct )
		$content = str_replace("'", '', $content);
}

function eztags_from_date(&$content)
{
	preg_match('/the_date\(([^\)]*)\);?/', $content, $matches);
	list($match, $attr) = $matches;

	$attrs = preg_split('/\,\s*/', $attr);
	$attrs = preg_replace('/\'/', '', $attrs);
	$attrs = preg_replace('/&quot;/', '', $attrs);
	list($format, $before, $after) = $attrs;

	if ( !$format )
		$content = str_replace($match, "?&gt;$before<\$EntryDate\$>$after&lt;?php", $content);
}

function eztags_from_e(&$content, $tag)
{
	$in_str = '';

	if ($tag == '_e')
		$suffix = 'Text';
	else if ($tag == '__')
		$suffix = 'String';

	while ($in_str != $content)
	{
		$in_str = $content;

		preg_match("/$tag\(([^\)]+)\);?/", $content, $matches);
		list($match, $tr) = $matches;

		list($text, $domain) = preg_split('/\,\s*/', $tr);

		$text = preg_replace('/\'/', '', $text);
		$text = preg_replace('/&quot;/', '', $text);

		$domain = preg_replace('/\'/', '', $domain);
		$domain = preg_replace('/&quot;/', '', $domain);

		if ( preg_match('/\s+/', $domain) )
		{
			$text .= ", $domain";
			$domain = '';
		}

		if ( $tag == '_e' )
		{
			$after = '&lt;?php';
			$before = '?&gt;';
		}

		$content = str_replace($match, "$before<Translatable$suffix:$domain>$text</Translatable$suffix>$after", $content);
	}

	$content = str_replace("<Translatable$suffix:>", "<Translatable$suffix>", $content);
}

function eztags_from_edit_comment_link(&$content)
{
	preg_match('/edit_comment_link\(([^\)]*)\);?/', $content, $matches);
	list($match, $attr) = $matches;

	$attrs = preg_split('/\,\s*/', $attr);
	$attrs = preg_replace('/\'/', '', $attrs);
	$attrs = preg_replace('/&quot;/', '', $attrs);
	list($link, $before, $after) = $attrs;

	$content = str_replace($match, "?&gt;$before<EditComment>$link</EditComment>$after&lt;?php", $content);
}

function eztags_from_edit_post_link(&$content)
{
	preg_match('/edit_post_link\(([^\)]*)\);?/', $content, $matches);
	list($match, $attr) = $matches;

	$attrs = preg_split('/\,\s*/', $attr);
	$attrs = preg_replace('/\'/', '', $attrs);
	$attrs = preg_replace('/&quot;/', '', $attrs);
	list($link, $before, $after) = $attrs;

	$content = str_replace($match, "?&gt;$before<EditEntry>$link</EditEntry>$after&lt;?php", $content);
}

function eztags_from_else(&$content)
{
	global $_eztags_in_ifentries;

	if ( !$_eztags_in_ifentries )
		$tag_name = 'WPElse';
	else
		$tag_name = 'WPIfNoEntries';

	$content = preg_replace('/else\s*:/', "?&gt;<\$$tag_name\$>&lt;?php", $content);
}

function eztags_from_end_if(&$content)
{
	global $_eztags_in_ifentries;
	$in_content = $content;

	if ( !$_eztags_in_ifentries )
		$tag_name = 'WPEndIf';
	else
		$tag_name = 'WPEndEntries';	

	$content = preg_replace('/endif[;\s]/', "?&gt;<\$$tag_name\$>&lt;?php", $content);

	if ( $in_content != $content ) $_eztags_in_ifentries = FALSE;
}

function eztags_from_end_loop(&$content)
{
	$content = preg_replace('/endwhile;?/', '?&gt;<$WPEndLoop$>&lt;?php', $content);
}

function eztags_from_entries_loop(&$content)
{
	$content = preg_replace('/while\s*\(\s*have_posts\(\)\s*\)\s*:\s*the_post\(\);?/m', '?&gt;<$WPEntriesLoop$>&lt;?php', $content);
}

function eztags_from_excerpt(&$content)
{
	$content = preg_replace('/the_excerpt\(\s*\);?/', '?&gt;<$EntryExcerpt$>&lt;?php', $content);
}

function eztags_from_foot(&$content)
{
	$content = preg_replace('/wp_footer\(\s*\);?/', '?&gt;<$WPFooter$>&lt;?php', $content);
}

function eztags_from_footer(&$content)
{
	$content = preg_replace('/get_footer\(\s*\);?/', '?&gt;<$WPLoadFooter$>&lt;?php', $content);
}

function eztags_from_get_archives(&$content)
{
	$content = preg_replace('/wp_get_archives\(\'?([^\']*)\'?\);?/', '?&gt;<$WPArchives:$1$>&lt;?php', $content);
}

function eztags_from_head(&$content)
{
	$content = preg_replace('/wp_head\(\s*\);?/', '?&gt;<$WPHeader$>&lt;?php', $content);
}

function eztags_from_header(&$content)
{
	$content = preg_replace('/get_header\(\s*\);?/', '?&gt;<$WPLoadHeader$>&lt;?php', $content);
}

function eztags_from_id(&$content)
{
	$content = preg_replace('/the_ID\(\s*\);?/', '?&gt;<$EntryID$>&lt;?php', $content);
}

function eztags_from_if_entries(&$content)
{
	global $_eztags_in_ifentries;
	$in_content = $content;

	$content = preg_replace('/if\s*\(\s*have_posts\(\)\s*\)\s*:/', '?&gt;<$WPIfEntries$>&lt;?php', $content);

	if ( $in_content != $content ) $_eztags_in_ifentries = TRUE;
}

function eztags_from_language_attributes(&$content)
{
	$content = preg_replace('/language_attributes\(\s*\);?/', '?&gt;<$WPLanguageAttributes$>&lt;?php', $content);
}

function eztags_from_link_pages(&$content)
{
	$content = preg_replace('/wp_link_pages\(\'?([^\']*)\'?\);?/', '?&gt;<$WPLinkPages:$1$>&lt;?php', $content);
}

function eztags_from_links_list(&$content)
{
	$content = preg_replace('/get_links_list\(\'?([^\']*)\'?\);?/', '?&gt;<$WPLinks:$1$>&lt;?php', $content);
	$content = str_replace('<$WPLinks:$>', '<$WPLinks:name$>', $content);
}

function eztags_from_list_bookmarks(&$content)
{
	$content = preg_replace('/wp_list_bookmarks\(\'?([^\']*)\'?\);?/', '?&gt;<$WPBookmarks:$1$>&lt;?php', $content);
}

function eztags_from_list_categories(&$content)
{
	$content = preg_replace('/wp_list_categories\(\'?([^\']*)\'?\);?/', '?&gt;<$WPCategories:$1$>&lt;?php', $content);
}

function eztags_from_list_cats(&$content)
{
	$content = preg_replace('/wp_list_cats\(\'?([^\']*)\'?\);?/', '?&gt;<$WPCategoriesOld:$1$>&lt;?php', $content);
}

function eztags_from_list_pages(&$content)
{
	$content = preg_replace('/wp_list_pages\(\s*\'?([^\']*)\'?\s*\);?/', '?&gt;<$WPPages:$1$>&lt;?php', $content);
}

function eztags_from_login(&$content)
{
	$content = preg_replace('/wp_loginout\(\s*\);?/', '?&gt;<$WPLoginOut$>&lt;?php', $content);
}

function eztags_from_meta(&$content)
{
	$content = preg_replace('/wp_meta\(\s*\);?/', '?&gt;<$WPMeta$>&lt;?php', $content);
}

function eztags_from_permalink(&$content)
{
	$content = preg_replace('/the_permalink\(\s*\);?/', '?&gt;<$EntryPermalink$>&lt;?php', $content);
}

function eztags_from_pings_open(&$content)
{
	$content = preg_replace('/if\s*\(\s*pings_open\(\s*\)\s*\)\s*:/', '?&gt;<$WPIfPingsOpen$>&lt;?php', $content);
}

function eztags_from_posts_nav_link(&$content)
{
	preg_match('/posts_nav_link\(([^\)]*)\);?/', $content, $matches);

	list($match, $params) = $matches;
	$params = str_replace("'", '', $params);

	list($sep, $prev, $next) = preg_split('/\s*\,\s*/', $params);

	$content = str_replace($match, "?&gt;<\$WPEntriesNavigation sep=\"$sep\" prev=\"$prev\" next=\"$next\"\$>&lt;?php", $content);
}

function eztags_from_post(&$content)
{
	$content = preg_replace('/the_post\(\s*\);?/', '?&gt;<$WPNextEntry$>&lt;?php', $content);
}

function eztags_from_query(&$content)
{
	preg_match('/query_posts\(([^\)]*)\);?/', $content, $matches);
	list($match, $query) = $matches;

	$query = str_replace("'", '', $query);
	$query = str_replace('&quot;', '', $query);

	$content = str_replace($match, "?&gt;<\$WPQuery:$query\$>&lt;?php", $content);
}

function eztags_from_register(&$content)
{
	preg_match('/wp_register\(([^\)]*)\);?/', $content, $matches);
	list($match, $attr) = $matches;

	$attrs = preg_split('/\,\s*/', $attr);
	$attrs = preg_replace('/\'/', '', $attrs);
	$attrs = preg_replace('/&quot;/', '', $attrs);
	list($before, $after) = $attrs;

	$content = str_replace($match, "?&gt;$before<\$WPRegister\$>$after&lt;?php", $content);
}

function eztags_from_rewind_posts(&$content)
{
	$content = preg_replace('/rewind_posts\(\);?/', '?&gt;<$WPRewind$>&lt;?php', $content);
}

function eztags_from_search_query(&$content)
{
	$content = preg_replace('/the_search_query\(\s*\);?/', '?&gt;<$WPSearch$>&lt;?php', $content);
}

function eztags_from_sidebar(&$content)
{
	$content = preg_replace('/get_sidebar\(\s*\);?/', '?&gt;<$WPLoadSidebar$>&lt;?php', $content);
}

function eztags_from_single_cat_title(&$content)
{
	$content = preg_replace('/single_cat_title\(\'?([^\']*)\'?\);?/', '?&gt;<CurrentCategory>$1</CurrentCategory>&lt;?php', $content);
}

function eztags_from_time(&$content)
{
	$content = preg_replace('/([^_])the_time\(\s*\);?/', '$1?&gt;<$EntryTime$>&lt;?php', $content);
	$content = preg_replace('/([^_])the_time\(\'([^\']+)\'\);?/', '$1?&gt;<$EntryTime:$2$>&lt;?php', $content);
}

function eztags_from_title(&$content)
{
	preg_match('/([^_])the_title\(([^\)]*)\);?/', $content, $matches);
	list($match, $bef, $attr) = $matches;

	$attrs = preg_split('/\,\s*/', $attr);
	$attrs = preg_replace('/\'/', '', $attrs);
	$attrs = preg_replace('/&quot;/', '', $attrs);
	list($before, $after) = $attrs;

	$content = str_replace($match, "$bef?&gt;$before<\$EntryTitle\$>$after&lt;?php", $content);
}

function eztags_from_trackback_rdf(&$content)
{
	$content = preg_replace('/trackback_rdf\(\);?/', '?&gt;<$WPAutodiscover$>&lt;?php', $content);
}

function eztags_from_trackback_url(&$content)
{
	$content = preg_replace('/trackback_url\((true)?\);?/i', '?&gt;<$EntryTrackbackURL$>&lt;?php', $content);
}

function eztags_from_wp_title(&$content)
{
	preg_match('/wp_title\(([^\)]*)\);?/', $content, $matches);
	list($match, $sep) = $matches;

	$sep = str_replace("'", '', $sep);
	$query = str_replace('&quot;', '', $query);

	$content = str_replace($match, "?&gt;<\$WPPageTitle:$sep\$>&lt;?php", $content);
	$content = str_replace('<$WPPageTitle:$>', '<$WPTitle$>', $content);
}

function eztags_parse_from(&$content)
{
	eztags_from_e($content, '__');

	eztags_from_author($content);
	eztags_from_author_link($content);
	eztags_from_author_posts($content);
	eztags_from_blog_info($content);
	eztags_from_calendar($content);
	eztags_from_category($content);
	eztags_from_comment_author($content);
	eztags_from_comment_author_link($content);
	eztags_from_comment_author_url($content);
	eztags_from_comment_id($content);
	eztags_from_comment_date($content);
	eztags_from_comment_text($content);
	eztags_from_comment_time($content);
	eztags_from_comments($content);
	eztags_from_comments_number($content);
	eztags_from_comments_open($content);
	eztags_from_comments_rss_link($content);
	eztags_from_content($content);
	eztags_from_date($content);
	eztags_from_e($content, '_e');
	eztags_from_edit_comment_link($content);
	eztags_from_edit_post_link($content);
	eztags_from_else($content);
	eztags_from_end_if($content);
	eztags_from_end_loop($content);
	eztags_from_entries_loop($content);
	eztags_from_excerpt($content);
	eztags_from_foot($content);
	eztags_from_footer($content);
	eztags_from_get_archives($content);
	eztags_from_head($content);
	eztags_from_header($content);
	eztags_from_id($content);
	eztags_from_if_entries($content);
	eztags_from_language_attributes($content);
	eztags_from_link_pages($content);
	eztags_from_links_list($content);
	eztags_from_list_bookmarks($content);
	eztags_from_list_categories($content);
	eztags_from_list_cats($content);
	eztags_from_list_pages($content);
	eztags_from_login($content);
	eztags_from_meta($content);
	eztags_from_permalink($content);
	eztags_from_pings_open($content);
	eztags_from_posts_nav_link($content);
	eztags_from_query($content);
	eztags_from_register($content);
	eztags_from_rewind_posts($content);
	eztags_from_search_query($content);
	eztags_from_sidebar($content);
	eztags_from_single_cat_title($content);
	eztags_from_time($content);
	eztags_from_title($content);
	eztags_from_trackback_rdf($content);
	eztags_from_trackback_url($content);
	eztags_from_wp_title($content);

	eztags_from_post($content);
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
