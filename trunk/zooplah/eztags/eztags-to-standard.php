<?php

function eztags_to_time(&$content)
{
	/* TODO: Get this thing working */
	preg_match_all('/<\$EntryTime([^\$]*)\$>/', $str, $matches);

	$attrs = $matches[1][0];

	preg_match('/format="(\S*)"/', $attrs, $matches2);

	list($match, $format) = $matches2;
	
	$content = str_replace($matches[0][0], "<?php the_time($format); ?>", $content);
}

function eztags_to_translatable(&$content)
{
	$content = str_replace('<TranslatableString>', '<?php _e(\'', $content);
	$content = str_replace('</TranslatableString>', '\'); ?>', $content);
}

function eztags_parse_ez(&$content)
{
	$content = str_replace('<$CommentAuthorLink$>', '<?php comment_author_link(); ?>', $content);
	$content = str_replace('<$CommentID$>', '<?php comment_ID(); ?>', $content);
	$content = str_replace('<$CommentText$>', '<?php comment_text(); ?>', $content);

	$content = str_replace('<$EntryTitle$>', '<?php the_title(); ?>', $content);

	$content = str_replace('<$EntryAuthor$>', '<?php the_author(); ?>', $content);
	$content = str_replace('<$EntryID$>', '<?php the_ID(); ?>', $content);
	$content = str_replace('<$EntryPermalink$>', '<?php the_permalink(); ?>', $content);
	$content = str_replace('<$LanguageAttributes$>', '<?php language_attributes(); ?>', $content);
	$content = str_replace('<$WPLoginOut$>', '<?php wp_loginout(); ?>', $content);

	//eztags_to_time($content);
	eztags_to_translatable($content);
}

?>
