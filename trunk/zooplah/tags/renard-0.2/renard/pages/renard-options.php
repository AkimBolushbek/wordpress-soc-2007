<?php
if ( $_GET['updated'] )
{
	update_option('renard_hide_non_php', $_GET['hide-non']);
	update_option('renard_editor_cols', $_GET['cols']);
	update_option('renard_editor_rows', $_GET['rows']);
}

?>

<?php

function get_dim($which)
{
	if ( get_option('renard_editor_' . $which) )
		echo get_option('renard_editor_' . $which);
	else
	{
		if ( 'cols' == $which )
			echo 70;
		else if ( 'rows' == $which )
			echo 25;
	}

}

?>

<form action="options-general.php">
<fieldset>
	<legend align="center" style="border: thin solid ButtonText; font-size: bigger; font-weight: bolder">Renard Options</legend>

<div>
<label for="hide-non"><?php _ez('Hide non-PHP files'); ?></label>
<input type="checkbox" name="hide-non" id="hide-non" <?php if ( 'on' == get_option('renard_hide_non_php') ) echo 'checked="checked"'; ?> />
</div>

<div>
<label for="cols"><?php _ez('Columns'); ?></label>
<input type="text" name="cols" id="cols" value="<?php get_dim('cols'); ?>" size="2" />
</div>

<div>
<label for="rows"><?php _ez('Rows'); ?></label>
<input type="text" name="rows" id="rows" value="<?php get_dim('rows'); ?>" size="2" />
</div>

<div>
<input type="hidden" name="page" value="renard/renard/pages/renard-options.php" />
<input type="hidden" name="updated" value="1" />
</div>

<div>
<input type="submit" />
</div>

</fieldset>
</form>
