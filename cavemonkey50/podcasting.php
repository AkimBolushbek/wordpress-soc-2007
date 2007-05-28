<?php

/*
Plugin Name: Podcasting
Version: 0.01
Plugin URI: http://code.google.com/p/wordpress-soc-2007/
Description: Adds full podcasting support.
Author: Ronald Heft, Jr.
Author URI: http://cavemonkey50.com/
*/

/* ------------------------------------- SETUP ------------------------------------- */

// Add Podcasting to the options menu
function add_podcasting_pages() {
	add_options_page('Podcasting Options', 'Podcasting', 8, basename(__FILE__), 'podcasting_options_page');
}

add_action('admin_menu', 'add_podcasting_pages');


/* ------------------------------------ OPTIONS ------------------------------------ */

// Podcasting options page
function podcasting_options_page() {
	?>
	
	<form method="post" action="options-general.php?page=podcasting.php">
	<div class="wrap">
		<h2>Podcasting Options</h2>
		
		<fieldset class="options">
			<legend>Podcast Details</legend>
		</fieldset>
		
		<fieldset class="options">
			<legend>iTunes Specifics</legend>
		</fieldset>
	</div>
	</form>
	
	<?php
} // End podcasting_options_page()


/* ------------------------------------- WORK -------------------------------------- */

?>