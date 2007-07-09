<?php

require_once 'eztagsoptions.php';

eztags_options_set_defaults();

if ( $_POST['nonphp'] && ('true' == $_POST['nonphp'] || 'false' == $_POST['nonphp']) ) eztags_option_non_php($_POST['nonphp']);

if ( $_POST['hidephp'] && ('yes' == $_POST['hidephp'] || 'no' == $_POST['hidephp']) ) eztags_option_hide_php($_POST['hidephp']);

$eztags_options['non_php'] = array('true' => __('Yes'), 'false' => __('No'));
$eztags_options['hide_php'] = array('yes' => __('Hide all PHP'), 'no' => __('Show some PHP'));

?>
<p />
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=eztagsoptionspage.php">
	<h2><?php _e('Show non-PHP Files?'); ?></h2>
	<p><?php _e('Some files (such as style sheets) can be edited, but won\'t go through the conversion process.  Do you want to be able to edit these files through this editor instead of the main theme editor?'); ?>
		<br />
<?php
while ( list($key, $val) = each($eztags_options['non_php']) ) :
?>
		<label for="nonphp-<?php echo strtolower($val); ?>"><?php echo $val; ?></label>
		<input type="radio" name="nonphp" id="nonphp-<?php echo strtolower($val); ?>" value="<?php echo $key; ?>" <?if ( get_option('show_non_php') == $key ) echo 'checked="checked"'; ?> />
<?php endwhile; ?>
	</p>
	<h2><?php _e('Hide all PHP?'); ?></h2>
	<p><?php _e('Some PHP code can\'t be reliably converted.  If you don\'t mind seeing some cryptic text, it\'s recommended for you to show the PHP code that can\'t reliably be converted.'); ?>
		<br />
		<select name="hidephp">
<?php
	while ( list($key, $val) = each($eztags_options['hide_php']) ) :
?>
			<option value="<?php echo $key; ?>" <?php if ( get_option('hide_php') == $key ) echo 'selected="selected"'; ?>><?php echo $val; ?></option>
<? endwhile; ?>
		</select>
	</p>
	<p class="submit">
		<input type="submit" value="<?php _e('Update Options &raquo;'); ?>" />
	</p>
</form>
