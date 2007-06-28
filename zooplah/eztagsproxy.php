<?php

/* Converts back and forth between the two types of template tags */

/* Strips PHP from the file */
function stripPHP(&$content)
{
	/* Preserve the special tags */
	function preserve(&$content)
	{
		$content = preg_replace('/&lt;\?php\s*\/\/\s*!!!/', '<!php', $content);
		$content = preg_replace('/\/\/\s*!!!\s*\?&gt;/', '!>', $content);
	}

	/* And restore 'em */
	function restore(&$content)
	{
		$content = str_replace('<!php', '<?php', $content);
		$content = str_replace('!>', '?>', $content);
	}

	preserve($content);

	/* Get rid of the PHP start and end markers */
	$content = preg_replace('/&lt;\?php\s*/', '', $content);
	$content = preg_replace('/\s*\?&gt;/', '', $content);

	restore($content);
}

/* Replace Standard tags with Easy tags */
function std2ez($content)
{
	stripPHP($content);

	/* The Actual tags */
	$content = str_replace('the_title();', '<$EntryTitle$>', $content);

	return $content;
}

/* Insert the markers indicating to-be-presevered PHP back in */
/* This would be so much easier in Esperanto.
 * I could just say enmetuKonservilojn
 * And additonally, you guys wouldn't be accusing me of making up words */
function insertPreservers(&$content)
{
	$content = str_replace('<?php', '<?php //!!!', $content);
	$content = str_replace('?>', '//!!! ?>', $content);
}

/* Replace Easy tags with Standard tags */
function ez2std($content)
{
	insertPreservers($content);

	$content = str_replace('<$EntryTitle$>', '<?php the_title(); ?>', $content);

	return $content;
}
