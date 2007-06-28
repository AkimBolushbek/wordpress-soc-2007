<?php

/* Insert the markers indicating to-be-presevered PHP back in */
/* This would be so much easier in Esperanto.
 * I could just say enmetuKonservilojn
 * And additonally, you guys wouldn't be accusing me of making up words */
function insertPreservers(&$content)
{
	$content = str_replace('<?php', '<?php //!!!', $content);
	$content = str_replace('?>', '//!!! ?>', $content);
}

?>
