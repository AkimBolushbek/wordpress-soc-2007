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

// Add the podcast feed
add_action('do_feed_podcast', 'do_feed_podcast');
add_filter('generate_rewrite_rules', 'podcasting_rewrite_rules');

// Add podcasting information to feeds
//add_filter('the_title_rss', 'test');


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

// Create a custom feed
function do_feed_podcast() {
	global $wp_query;
	$wp_query->get_posts();
	load_template(ABSPATH . 'wp-rss2.php');
}

// Pretty permalinks for the custom feed
function podcasting_rewrite_rules($wp_rewrite) {
    $feed_rules = array(
		'feed/podcast' => 'index.php?feed=podcast',
	);
	$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
}

/* function test($title) {
	if ( 'podcast' == get_query_var('feed') )
		return 'Podcast: ' . $title . get_query_var('feed');
	else
		return $title;
} */

?>