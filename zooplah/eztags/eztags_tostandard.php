<?php

function eztags_parse_ez(&$content)
{
	$content = str_replace('<$EntryTitle lang="en"$>', '<?php the_title(); ?>', $content);
}

?>
