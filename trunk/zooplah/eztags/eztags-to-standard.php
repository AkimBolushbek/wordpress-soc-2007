<?php

function eztags_to_translatable(&$content)
{
	$content = str_replace('<TranslatableString>', '<?php _e(\'', $content);
	$content = str_replace('</TranslatableString>', '\'); ?>', $content);
}

function eztags_parse_ez(&$content)
{
	$content = str_replace('<$EntryTitle lang="en"$>', '<?php the_title(); ?>', $content);
	$content = str_replace('<$EntryAuthor$>', '<?php the_author(); ?>', $content);

	eztags_to_translatable($content);
}

?>
