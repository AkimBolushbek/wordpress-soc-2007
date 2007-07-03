<?php

/* Mediates between the two types of template tags */

require_once 'eztags_fromstandard.php';
require_once 'eztags_tostandard.php';

/* Replace Standard tags with Easy tags */
function std2ez($content)
{
	//stripPHP($content);

	/* The Actual tags */
	/*$content = preg_replace('/if\s*\(have_posts\(\)\)\s*:\s*while\s*\(have_posts\(\)\)\s*:\s*the_post\(\);/m', '<Entries>', $content);
	$content = str_replace('endwhile;', "</Entries>", $content);
	$content = preg_replace('/else\s*:/', '<NoEntries>', $content);
	$content = str_replace('endif;', '</NoEntries>', $content);

	$content = str_replace('get_header();', '<$Header$>', $content);
	$Content = str_replace('the_author();', '<$EntryAuthor$>', $content);
	$content = preg_replace('/the_date\([^\)]+\);/', '<$EntryDate$>', $content);
	$content = str_replace('the_ID();', '<$EntryID$>', $content);
	$content = preg_replace('/the_permalink\(\);?/', '<$EntryPermalink$>', $content);
	$content = str_replace('the_time();', '<$EntryTime$>', $content);
	$content = str_replace('the_title();', '<$EntryTitle$>', $content);
	$content = str_replace('wp_link_pages();', '<$LinkPages$>', $content);*/

	return $content;
}

/* Replace Easy tags with Standard tags */
function ez2std($content)
{
	/*insertPreservers($content);

	$content = str_replace('<Entries>' , '<?php if (have_posts()): while (have_posts()): the_post(); ?>', $content);
	$content = str_replace('</Entries', '<?php endwhile; ?>', $content);
	$content = str_replace('<NoEntries>', '<?php else: ?>', $content);
	$content = str_replace('</NoEntries>', '<?php endif; ?>', $content);

	$content = str_replace('<$EntryAuthor', '<?php the_author();', $content);
	$content = str_replace('<$EntryDate$>', "<?php the_date('', '<h2>', '</h2>')", $content);
	$content = str_replace('<$EntryId$>', '<?php the_id(); ?>', $content);
	$content = str_replace('<$EntryPermalink$>', '<?php the_permalink(); ?>', $content);
	$content = str_replace('<$EntryTime$>', '<?php the_time(); ?>', $content);
	$content = str_replace('<$EntryTitle$>', '<?php the_title(); ?>', $content);
	$content = str_replace('<$Header$>', '<?php get_header(); ?>', $content);
	$content = str_replace('<$LinkPages$>', '<?php wp_link_pages(); ?>', $content); */

	return $content;
}
