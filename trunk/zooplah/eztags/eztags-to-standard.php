<?php

function eztags_to_comments_rss_link($content)
{
	$content = str_replace('<CommentsRSSLink>', '<?php comments_rss_link(', $content);
	$content = str_replace('</CommentsRSSLink>', '); ?>', $content);
}

function eztags_to_post_nav_link(&$content)
{
	preg_match('/<\$WPEntriesNavigation([^\$]+)\$>/', $content, $matches);

	list($match, $attrs) = $matches;

	preg_match_all('/\w+\="([^"]*)"/', $attrs, $matches2);

	for ($i = 0; $i < count($matches2[1]); $i++)
	{
		if ( '__' != substr($matches2[1][$i], 0, 2) )
			$matches2[1][$i] = '\'' . $matches2[1][$i] . '\'';
	}
	
	reset($matches2);
	$content = str_replace($match, '<?php posts_nav_link(' . join($matches2[1], ', ') . '); ?>', $content);
}

function eztags_to_translatable(&$content, $tag)
{
	if ( $tag == '_e' )
	{
		$after = ' ?>';
		$before='<?php ';
		$sc = ';';
		$suffix = 'Text';
	}
	else if ( $tag == '__' )
	{
		$sc = '';
		$suffix = 'String';
	}

	// Without a domain
	$content = preg_replace("/<Translatable$suffix>([^<]*)<\/Translatable$suffix>/", "$before$tag('$1')$sc$after", $content);

	// With a domain
	$content = preg_replace("/<Translatable$suffix:([^>]+)>([^<]*)<\/Translatable$suffix>/", "$before$tag('$2', '$1')$sc$after", $content);
}

function eztags_to_from_element(&$content, $in_re, $tag)
{
	preg_match($in_re, $content, $matches);
	list($match, $text) = $matches;

	if ( '__' != substr($text, 0, 2) )
	{
		$text_delim = "'";
	}

	$content = str_replace($match, "<?php $tag($text_delim$text$text_delim); ?>", $content);
}

function eztags_parse_ez(&$content)
{
	eztags_to_translatable($content, '__');
	$content = str_replace('<$CommentAuthor$>', '<?php comment_author(); ?>', $content);
	$content = str_replace('<$CommentAuthorLink$>', '<?php comment_author_link(); ?>', $content);
	$content = str_replace('<$CommentAuthorURL$>', '<?php comment_author_url(); ?>', $content);
	$content = str_replace('<$CommentDate$>', '<?php comment_date(); ?>', $content);
	$content = preg_replace('/<\$CommentDate:([^\$]+)\$>/', '<?php comment_date(\'$1\'); ?>', $content);
	$content = str_replace('<$CommentID$>', '<?php comment_ID(); ?>', $content);
	$content = str_replace('<$CommentText$>', '<?php comment_text(); ?>', $content);
	$content = str_replace('<$CommentTime$>', '<?php comment_time(); ?>', $content);

	$content = str_replace('<$EntryTitle$>', '<?php the_title(); ?>', $content);

	$content = str_replace('<$EntryAuthor$>', '<?php the_author(); ?>', $content);
	$content = str_replace('<$EntryAuthorLink$>', '<?php the_author_link(); ?>', $content);
	$content = str_replace('<$EntryAuthorPostsLink$>', '<?php the_author_posts_link(); ?>', $content);
	$content = str_replace('<$EntryDate$>', '<?php the_date(); ?>', $content);
	$content = str_replace('<$EntryExcerpt$>', '<?php the_excerpt(); ?>', $content);
	$content = str_replace('<$EntryID$>', '<?php the_ID(); ?>', $content);
	$content = str_replace('<$EntryPermalink$>', '<?php the_permalink(); ?>', $content);
	$content = str_replace('<$EntryTime$>', '<?php the_time(); ?>', $content);
	$content = preg_replace('/<\$EntryTime:([^\$]*)\$>/', '<?php the_time(\'$1\'); ?>', $content);
	$content = str_replace('<$EntryTrackbackURL$>', '<?php trackback_url(); ?>', $content);

	$content = preg_replace('/<\$WPArchives:([^\$]*)\$>/', '<?php wp_get_archives(\'$1\'); ?>', $content);
	$content = str_replace('<$WPAutodiscover$>', '<?php trackback_rdf(); ?>', $content);
	$content = preg_replace('/<\$WPBookmarks:([^\$]*)\$>/', '<?php wp_list_bookmarks(\'$1\'); ?>', $content);
	$content = str_replace('<$WPCalendar$>', '<?php get_calendar(true); ?>', $content);
	$content = str_replace('<$WPCalendar3$>', '<?php get_calendar(false); ?>', $content);
	$content = preg_replace('/<\$WPCategories:([^\$]+)\$>/', '<?php wp_list_categories(\'$1\'); ?>', $content);
	$content = preg_replace('/<\$WPCategoriesOld:([^\$]+)\$>/', '<?php wp_list_cats(\'$1\'); ?>', $content);
	$content = str_replace('<$WPElse$>', '<?php else : ?>', $content);
	$content = str_replace('<$WPEndEntries$>', '<?php endif; ?>', $content);
	$content = str_replace('<$WPEndIf$>', '<?php endif; ?>', $content);
	$content = str_replace('<$WPEndLoop$>', '<?php endwhile; ?>', $content);
	$content = str_replace('<$WPEntriesLoop$>', '<?php while (have_posts()) : the_post(); ?>', $content);
	$content = str_replace('<$WPFooter$>', '<?php wp_footer(); ?>', $content);
	$content = str_replace('<$WPHeader$>', '<?php wp_head(); ?>', $content);
	$content = str_replace('<$WPIfEntries$>', '<?php if ( have_posts() ) : ?>', $content);
	$content = str_replace('<$WPIfCommentsOpen$>', '<?php if ( comments_open() ) : ?>', $content);
	$content = str_replace('<$WPIfNoEntries$>', '<?php else : ?>', $content);
	$content = str_replace('<$WPIfPingsOpen$>', '<?php if ( pings_open() ) : ?>', $content);
	$content = preg_replace('/<\$WPInfo:([^\$]+)\$>/', '<?php bloginfo(\'$1\'); ?>', $content);
	$content = str_replace('<$WPLanguageAttributes$>', '<?php language_attributes(); ?>', $content);
	$content = preg_replace('/<\$WPLinkPages:([^\$]*)\$>/', '<?php wp_link_pages(\'$1\'); ?>', $content);
	$content = preg_replace('/<\$WPLinks:([^\$]+)\$>/', '<?php get_links_list(\'$1\'); ?>', $content);
	$content = str_replace('<$WPLoadComments$>', '<?php comments_template(); ?>', $content);
	$content = str_replace('<$WPLoadFooter$>', '<?php get_footer(); ?>', $content);
	$content = str_replace('<$WPLoadHeader$>', '<?php get_header(); ?>', $content);
	$content = str_replace('<$WPLoadSidebar$>', '<?php get_sidebar(); ?>', $content);
	$content = str_replace('<$WPLoginOut$>', '<?php wp_loginout(); ?>', $content);
	$content = str_replace('<$WPMeta$>', '<?php wp_meta(); ?>', $content);
	$content = str_replace('<$WPNextEntry$>', '<?php the_post(); ?>', $content);
	$content = preg_replace('/<\$WPPages:([^\$]*)\$>/', '<?php wp_list_pages(\'$1\'); ?>', $content);
	$content = str_replace('<$WPPageTitle$>', '<?php wp_title(); ?>', $content);
	$content = preg_replace('/<\$WPPageTitle:([^\$]*)\$>/', '<?php wp_title(\'$1\'); ?>', $content);
	$content = preg_replace('/<\$WPQuery:([^\$]*)\$>/', '<?php 
	query_posts(\'$1\'); ?>', $content);
	$content = str_replace('<$WPRegister$>', '<?php wp_register(); ?>', $content);
	$content = str_replace('<$WPRewind$>', '<?php rewind_posts(); ?>', $content);
	$content = str_replace('<$WPSearch$>', '<?php the_search_query(); ?>', $content);

	eztags_to_from_element($content, '/<CurrentCategory>([^>]*)<\/CurrentCategory>/', 'single_cat_title');
	eztags_to_from_element($content, '/<EditComment>([^>]*)<\/EditComment>/', 'edit_comment_link');
	eztags_to_from_element($content, '/<EditEntry>([^<]*)<\/EditEntry>/', 'edit_post_link');
	eztags_to_from_element($content, '/<EntryCategories>([^>]*)<\/EntryCategories>/', 'the_category');
	eztags_to_from_element($content, '/<EntryContent>([^>]*)<\/EntryContent>/', 'the_content');

	eztags_to_comments_rss_link($content);
	eztags_to_post_nav_link($content);
	eztags_to_translatable($content, '_e');
}

?>
