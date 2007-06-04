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

// Add Podcasting options to the database
add_option('pod_title', get_option('blogname'), "The podcast's title");
add_option('pod_tagline', get_option('blogdescription'), "The podcast's tagline");
add_option('pod_itunes_summary', '', 'iTunes summary');
add_option('pod_itunes_author', '', 'iTunes author');
add_option('pod_itunes_image', '', 'iTunes image');
add_option('pod_itunes_cat1', '', 'iTunes category 1');
add_option('pod_itunes_cat2', '', 'iTunes category 2');
add_option('pod_itunes_cat3', '', 'iTunes category 3');
add_option('pod_itunes_keywords', '', 'iTunes keywords');
add_option('pod_itunes_explicit', '', 'iTunes explicit');
add_option('pod_itunes_ownername', '', 'iTunes owner name');
add_option('pod_itunes_owneremail', '', 'iTunes owner email');
add_option('pod_itunes_block', '', 'Block iTunes');

// Add Podcasting to the options menu
function add_podcasting_pages() {
	add_options_page('Podcasting Options', 'Podcasting', 8, basename(__FILE__), 'podcasting_options_page');
}
add_action('admin_menu', 'add_podcasting_pages');

// Add the podcast feed
add_action('do_feed_podcast', 'do_feed_podcast');
add_filter('generate_rewrite_rules', 'podcasting_rewrite_rules');

// Add podcasting information to feeds
add_action('rss2_ns', 'podcasting_add_itunes_xml');
add_filter('option_blogname', 'podcasting_blogname_filter');
add_filter('option_blogdescription', 'podcasting_blogdescription_filter');
add_action('rss2_head', 'podcasting_add_itunes_feed');


/* ------------------------------------ OPTIONS ------------------------------------ */

// wp_nonce
if ( !function_exists('wp_nonce_field') ) {
	function podcasting_nonce_field($action = -1) { return; }
	$podcasting_nonce = -1;
} else {
	function podcasting_nonce_field($action = -1) { return wp_nonce_field($action); }
	$podcasting_nonce = 'podcasting-update-key';
}

// Podcasting options page
function podcasting_options_page() {
	// Store options if postback
	if ( isset($_POST['Submit']) ) {
		// Prevent attacks
		check_admin_referer('$podcasting_nonce', $podcasting_nonce);
		
		// Update the podcast options
		update_option('pod_title', $_POST[pod_title]);
		update_option('pod_tagline', $_POST[pod_tagline]);
		
		// Update the iTunes options
		update_option('pod_itunes_summary', $_POST[pod_itunes_summary]);
		update_option('pod_itunes_author', $_POST[pod_itunes_author]);
		update_option('pod_itunes_image', $_POST[pod_itunes_image]);
		update_option('pod_itunes_cat1', $_POST[pod_itunes_cat1]);
		update_option('pod_itunes_cat2', $_POST[pod_itunes_cat2]);
		update_option('pod_itunes_cat3', $_POST[pod_itunes_cat3]);
		update_option('pod_itunes_keywords', $_POST[pod_itunes_keywords]);
	}
	
	// iTunes category options
	$pod_itunes_cats = array(
		'Arts', 'Arts||Design', 'Arts||Fashion &amp; Beauty', 'Arts||Food', 'Arts||Literature', 'Arts||Performing Arts', 'Arts||Visual Arts',
		'Business', 'Business||Business News', 'Business||Careers', 'Business||Investing', 'Business||Management &amp; Marketing', 'Business||Shopping',
		'Comedy',
		'Education', 'Education||Education Technology', 'Education||Higher Education', 'Education||K-12', 'Education||Language Courses', 'Education||Training',
		'Games &amp; Hobbies', 'Games &amp; Hobbies||Automotive', 'Games &amp; Hobbies||Aviation', 'Games &amp; Hobbies||Hobbies', 'Games &amp; Hobbies||Other Games', 'Games &amp; Hobbies||Video Games',
		'Government &amp; Organizations', 'Government &amp; Organizations||Local', 'Government &amp; Organizations||National', 'Government &amp; Organizations||Non-Profit', 'Government &amp; Organizations||Regional',
		'Health', 'Health||Alternative Health', 'Health||Fitness &amp; Nutrition', 'Health||Self-Help', 'Health||Sexuality',
		'Kids &amp; Family',
		'Music',
		'News &amp; Politics',
		'Religion &amp; Spirituality', 'Religion &amp; Spirituality||Buddhism', 'Religion &amp; Spirituality||Christianity', 'Religion &amp; Spirituality||Hinduism', 'Religion &amp; Spirituality||Islam', 'Religion &amp; Spirituality||Judaism', 'Religion &amp; Spirituality||Other', 'Religion &amp; Spirituality||Spirituality',
		'Science &amp; Medicine', 'Science &amp; Medicine||Medicine', 'Science &amp; Medicine||Natural Sciences', 'Science &amp; Medicine||Social Sciences',
		'Society &amp; Culture', 'Society &amp; Culture||History', 'Society &amp; Culture||Personal Journals', 'Society &amp; Culture||Philosophy', 'Society &amp; Culture||Places &amp Travel',
		'Sports &amp; Recreation', 'Sports &amp; Recreation||Amateur', 'Sports &amp; Recreation||College &amp; High School', 'Sports &amp; Recreation||Outdoor', 'Sports &amp; Recreation||Professional',
		'Technology', 'Technology||Gadgets', 'Technology||Tech News', 'Technology||Podcasting', 'Technology||Software How-To',
		'TV &amp; Film'
		);
	?>
	
	<form method="post" action="options-general.php?page=podcasting.php">
	<?php podcasting_nonce_field('$podcasting_nonce', $podcasting_nonce); ?>
	<div class="wrap">
		<h2>Podcasting Options</h2>
		
		<p class="submit">
			<input type="submit" name="Submit" value="Update Options &raquo;" />
		</p>
		
		<fieldset class="options">
			<table class="optiontable">
				<tr valign="top">
					<th scope="row">
						<label for="pod_title">Podcast feed address (URL):</label>
					</th>
					<td>
						<p style="margin: 3px 0;"><strong>
							<?php echo get_option('siteurl');
							global $wp_rewrite;
							if ($wp_rewrite->using_permalinks())
								echo "/feed/podcast/";
							else
								echo "/?feed=podcast"; ?>
						</strong></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="pod_title">Title:</label>
					</th>
					<td>
						<input type="text" size="40" name="pod_title" id="pod_title" value="<?php echo stripslashes(get_option('pod_title')); ?>" />
						<br />If your podcast's title is different then your blog's title, change the title here.
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="pod_tagline">Podcast tagline:</label>
					</th>
					<td>
						<input type="text" style="width: 95%" name="pod_tagline" id="pod_tagline" value="<?php echo stripslashes(get_option('pod_tagline')); ?>" />
						<br />If your podcast's tagline is different then your blog's tagline, change the tagline here.
					</td>
				</tr>
			</table>
		</fieldset>
		
		<fieldset class="options">
			<legend>iTunes Specifics</legend>
			<table class="optiontable">
				<tr valign="top">
					<th scope="row">
						<label for="pod_itunes_summary">Summary:</label>
					</th>
					<td>
						<textarea cols="40" rows="4" style="width: 95%" name="pod_itunes_summary" id="pod_itunes_summary"><?php echo stripslashes(get_option('pod_itunes_summary')); ?></textarea>
						<br />A detailed description of your podcast. iTunes allows up to 4,000 characters and the tagline will be used if no summary is entered.
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="pod_itunes_author">Author:</label>
					</th>
					<td>
						<input type="text" size="40" name="pod_itunes_author" id="pod_itunes_author" value="<?php echo stripslashes(get_option('pod_itunes_author')); ?>" />
						<br />The default author of your podcast.
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="pod_itunes_image">Podcast Art (URL):</label>
					</th>
					<td>
						<input type="text" size="40" name="pod_itunes_image" id="pod_itunes_image" value="<?php echo stripslashes(get_option('pod_itunes_image')); ?>" />
						<br />An image which represents your podcast. iTunes uses this image on your podcast directory page and a smaller version in searches. iTunes prefers square .jpg images that are at least 300 x 300 pixels, but any jpg or png will work.
					</td>
				</tr>
				<?php for ($i = 1; $i <= 3; $i++) {
				$pod_cat_option = 'pod_itunes_cat' . $i;
				$pod_cat_label = ( 1 == $i ) ? 'Primary Category' : 'Category ' . $i;
				$pod_cat_summary = ( 1 == $i ) ? 'The category which most fits your podcast. The primary category is used in Top Podcasts lists and directory pages which include podcast art.' : 'An optional additional category which is only used on directory pages without podcast art.';
				?>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo $pod_cat_option; ?>"><?php echo $pod_cat_label; ?>:</label>
					</th>
					<td>
						<select name="<?php echo $pod_cat_option; ?>" id="<?php echo $pod_cat_option; ?>">
							<option value=""></option>
							<?php foreach ( $pod_itunes_cats as $pod_itunes_cat ) {
								// Deal with subcategories
								$pod_category = explode("||", $pod_itunes_cat);
								$pod_category_display = ( $pod_category[1] ) ? '&nbsp;&nbsp;&nbsp;' . $pod_category[1] : $pod_category[0];
								// If selected category
								$pod_selected = ( $pod_itunes_cat == htmlspecialchars(stripslashes(get_option($pod_cat_option))) ) ? ' selected="selected"' : '';

								echo '<option value="' . $pod_itunes_cat . '"' . $pod_selected . '>' . $pod_category_display . '</option>';
							} ?>
						</select>
						<br /><?php echo $pod_cat_summary; ?>
					</td>
				</tr>
				<?php } ?>
				<tr valign="top">
					<th scope="row">
						<label for="pod_itunes_keywords">Keywords:</label>
					</th>
					<td>
						<input type="text" style="width: 95%" name="pod_itunes_keywords" id="pod_itunes_keywords" value="<?php echo stripslashes(get_option('pod_itunes_keywords')); ?>" />
						<br />Up to 12 comma-separated words which iTunes uses for search placement. 
					</td>
				</tr>
			</table>
		</fieldset>
		
		<p class="submit">
			<input type="submit" name="Submit" value="Update Options &raquo;" />
		</p>
	</div>
	</form>
	
	<?php
} // podcasting_options_page()


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

// Add the iTunes xml information
function podcasting_add_itunes_xml() {
	if ( 'podcast' == get_query_var('feed') ) {
		echo 'xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"';
	}
}

// Change the podcast title
function podcasting_blogname_filter($title) {
	if ( 'podcast' == get_query_var('feed') )
		$title = stripslashes(get_option('pod_title'));
	return $title;
}

// Change the podcast tagline
function podcasting_blogdescription_filter($tagline) {
	if ( 'podcast' == get_query_var('feed') )
		$tagline = stripslashes(get_option('pod_tagline'));
	return $tagline;
}

// Add the special iTunes information to channel
function podcasting_add_itunes_feed() {
	if ( 'podcast' == get_query_var('feed') ) {
		// iTunes summary
		if ( '' != get_option('pod_itunes_summary') )
			echo '<itunes:summary>' . stripslashes(get_option('pod_itunes_summary')) . '</itunes:summary>' . "\n	";
		// iTunes subtitle
		if ( '' != get_option('pod_tagline') )
			echo '<itunes:subtitle>' . stripslashes(get_option('pod_tagline')) . '</itunes:subtitle>' . "\n	";
		// iTunes author
		if ( '' != get_option('pod_itunes_author') )
			echo '<itunes:author>' . stripslashes(get_option('pod_itunes_author')) . '</itunes:author>' . "\n	";
		// iTunes image
		if ( '' != get_option('pod_itunes_image') )
			echo '<itunes:image href="' . stripslashes(get_option('pod_itunes_image')) . '" />' . "\n	";
		// iTunes categories
		for ($i = 1; $i <= 3; $i++) {
			$pod_cat_option = 'pod_itunes_cat' . $i;
			if ( '' != get_option($pod_cat_option) ) {
				$pod_category = explode('||', htmlspecialchars(stripslashes(get_option($pod_cat_option))));
				if ( $pod_category[1] ) {
					echo '<itunes:category text="' . $pod_category[0] . '">' . "\n		";
					echo '<itunes:category text="' . $pod_category[1] . '" />' . "\n	";
					echo '</itunes:category>' . "\n	";
				} else
					echo '<itunes:category text="' . $pod_category[0] . '" />' . "\n	";
			}
		}
		// iTunes keywords
		if ( '' != get_option('pod_itunes_keywords') )
			echo '<itunes:keywords>' . stripslashes(get_option('pod_itunes_keywords')) . '</itunes:keywords>' . "\n	";
	}
} // podcasting_add_itunes_feed()

?>