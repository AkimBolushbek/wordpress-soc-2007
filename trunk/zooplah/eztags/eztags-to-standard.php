<?php

function eztags_to_translatable(&$content)
{
	$content = str_replace('<TranslatableString>', '<?php _e(\'', $content);
	$content = str_replace('</TranslatableString>', '\'); ?>', $content);
}

function eztags_parse_ez(&$content)
{
	$content = str_replace('<$CommentAuthorLink$>', '<?php comment_author_link(); ?>', $content);
	$content = str_replace('<$CommentDate$>', '<?php comment_date(); ?>', $content);
	$content = str_replace('<$CommentID$>', '<?php comment_ID(); ?>', $content);
	$content = str_replace('<$CommentText$>', '<?php comment_text(); ?>', $content);
	$content = str_replace('<$CommentTime$>', '<?php comment_time(); ?>', $content);

	$content = str_replace('<$EntryTitle$>', '<?php the_title(); ?>', $content);

	$content = str_replace('<$EntryAuthor$>', '<?php the_author(); ?>', $content);
	$content = str_replace('<$EntryID$>', '<?php the_ID(); ?>', $content);
	$content = str_replace('<$EntryPermalink$>', '<?php the_permalink(); ?>', $content);
	$content = str_replace('<$EntryTime$>', '<?php the_time(); ?>', $content);
	$content = str_replace('<$LanguageAttributes$>', '<?php language_attributes(); ?>', $content);
	$content = str_replace('<$WPCalendar$>', '<?php get_calendar(true); ?>', $content);
	$content = str_replace('<$WPCalendar3$>', '<?php get_calendar(false); ?>', $content);
	$content = str_replace('<$WPLoginOut$>', '<?php wp_loginout(); ?>', $content);

	$content = preg_replace('/<EntryCategory>([^>]*)<\/EntryCategory>/', '<?php the_category(\'$1\'); ?>', $content);

	eztags_to_translatable($content);
}

?>
