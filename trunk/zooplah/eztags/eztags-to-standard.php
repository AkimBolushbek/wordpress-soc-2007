<?php

function eztags_to_translatable(&$content)
{
	$content = str_replace('<TranslatableString>', '<?php _e(\'', $content);
	$content = str_replace('</TranslatableString>', '\'); ?>', $content);
}

function eztags_parse_ez(&$content)
{
	$content = str_replace('<$CommentAuthor$>', '<?php comment_author(); ?>', $content);
	$content = str_replace('<$CommentAuthorLink$>', '<?php comment_author_link(); ?>', $content);
	$content = str_replace('<$CommentAuthorURL$>', '<?php comment_author_url(); ?>', $content);
	$content = str_replace('<$CommentDate$>', '<?php comment_date(); ?>', $content);
	$content = preg_replace('/<\$CommentDate:([^\$]+)\$>/', '<?php comment_date(\'$1\'); ?>', $content);
	$content = str_replace('<$CommentID$>', '<?php comment_ID(); ?>', $content);
	$content = str_replace('<$CommentText$>', '<?php comment_text(); ?>', $content);
	$content = str_replace('<$CommentTime$>', '<?php comment_time(); ?>', $content);

	$content = str_replace('<$EntryTitle$>', '<?php the_title(); ?>', $content);

	$content = str_replace('<$EntryAuthor$>', '<?php the_author(); ?>', $content);	$content = str_replace('<$EntryAuthorPostsLink$>', '<?php the_author_posts_link(); ?>', $content);
	$content = str_replace('<$EntryDate$>', '<?php the_date(); ?>', $content);
	$content = str_replace('<$EntryExcerpt$>', '<?php the_excerpt(); ?>', $content);
	$content = str_replace('<$EntryID$>', '<?php the_ID(); ?>', $content);
	$content = str_replace('<$EntryPermalink$>', '<?php the_permalink(); ?>', $content);
	$content = str_replace('<$EntryTime$>', '<?php the_time(); ?>', $content);
	$content = preg_replace('/<\$EntryTime:([^\$]*)\$>/', '<?php the_time(\'$1\'); ?>', $content);
	$content = str_replace('<$EntryTrackbackURL$>', '<?php trackback_url(); ?>', $content);

	$content = preg_replace('/<\$WPArchives:([^\$]*)\$>/', '<?php wp_get_archives(\'$1\'); ?>', $content);
	$content = preg_replace('/<\$WPBookmarks:([^\$]*)\$>/', '<?php wp_list_bookmarks(\'$1\'); ?>', $content);
	$content = str_replace('<$WPCalendar$>', '<?php get_calendar(true); ?>', $content);
	$content = str_replace('<$WPCalendar3$>', '<?php get_calendar(false); ?>', $content);
	$content = preg_replace('/<\$WPCategories:([^\$]+)\$>/', '<?php wp_list_categories(\'$1\'); ?>', $content);
	$content = preg_replace('/<\$WPCategoriesOld:([^\$]+)\$>/', '<?php wp_list_cats(\'$1\'); ?>', $content);
	$content = preg_replace('/<\$WPInfo:([^\$]+)\$>/', '<?php bloginfo(\'$1\'); ?>', $content);
	$content = str_replace('<$WPLanguageAttributes$>', '<?php language_attributes(); ?>', $content);
	$content = preg_replace('/<\$WPLinkPages:([^\$]*)\$>/', '<?php wp_link_pages(\'$1\'); ?>', $content);
	$content = preg_replace('/<\$WPLinks:([^\$]+)\$>/', '<?php get_links_list(\'$1\'); ?>', $content);
	$content = str_replace('<$WPLoginOut$>', '<?php wp_loginout(); ?>', $content);
	$content = str_replace('<$WPMeta$>', '<?php wp_meta(); ?>', $content);
	$content = preg_replace('/<\$WPPages:([^\$]*)\$>/', '<?php wp_list_pages(\'$1\'); ?>', $content);
	$content = str_replace('<$WPRegister$>', '<?php wp_register(); ?>', $content);
	$content = str_replace('<$WPSearch$>', '<?php the_search_query(); ?>', $content);

	$content = preg_replace('/<CurrentCategory>([^>]*)<\/CurrentCategory>/', '<?php single_cat_title(\'$1\'); ?>', $content);
	$content = preg_replace('/<EntryCategories>([^>]*)<\/EntryCategories>/', '<?php the_category(\'$1\'); ?>', $content);
	$content = preg_replace('/<EntryContent>([^>]*)<\/EntryContent>/', '<?php the_content(__(\'$1\')); ?>', $content);

	eztags_to_translatable($content);
}

?>