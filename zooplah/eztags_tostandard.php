<?php

function eztags_parse_ez(&$content)
{
	$content = str_replace('<$EntryTitle$>', '<?php the_title(); ?>', $content);
}

?>
