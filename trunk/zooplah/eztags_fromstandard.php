<?php
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

/* Strips PHP from the file */
function stripPHP(&$content)
{
	preserve($content);

	/* Get rid of the PHP start and end markers */
	$content = preg_replace('/&lt;\?php\s*/', '', $content);
	$content = preg_replace('/\s*\?&gt;/', '', $content);

	restore($content);
}

?>
