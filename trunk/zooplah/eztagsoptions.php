<?php

function eztags_update_option($key, $value)
{
	update_option($key, $value);
	update_option('eztags_options_set', 'true');
}

function eztags_option_non_php($value)
{
	eztags_update_option('show_non_php', $value);
}

function eztags_option_hide_php($value)
{
	eztags_update_option('hide_php', $value);
}

function eztags_options_set_defaults()
{
	if ( 'true' != get_option('eztags_options_set') )
	{
		eztags_option_non_php('true');
		eztags_option_hide_php('yes');
	}
}

?>
