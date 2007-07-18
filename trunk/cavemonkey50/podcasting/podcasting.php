<?php

/*
Plugin Name: Podcasting
Version: 0.5
Plugin URI: http://code.google.com/p/wordpress-soc-2007/
Description: Adds full podcasting support.
Author: Ronald Heft, Jr.
Author URI: http://cavemonkey50.com/
*/

/* ------------------------------------- SETUP ------------------------------------- */

// Install podcasting
add_action('activate_podcasting/podcasting.php', 'podcasting_install');
add_action('init', 'podcasting_init');
add_action('admin_menu', 'add_podcasting_pages');

// Add post page information
add_action('admin_head', 'podcasting_admin_head');
add_action('dbx_post_advanced', 'podcasting_edit_form');

// Save post page information
add_action('save_post', 'podcasting_save_form');
add_action('delete_post', 'podcasting_delete_form');

// Add the podcast feed
add_filter('generate_rewrite_rules', 'podcasting_rewrite_rules');
add_filter('query_vars', 'podcasting_query_vars');
add_filter('posts_join', 'podcasting_feed_join');
add_filter('posts_where', 'podcasting_feed_where');
add_filter('posts_groupby', 'podcasting_feed_groupby');
add_action('wp_head', 'podcasting_add_feed_discovery');

// Add podcasting information to feeds
add_action('rss2_ns', 'podcasting_add_itunes_xml');
add_filter('option_blogname', 'podcasting_blogname_filter');
add_filter('option_blogdescription', 'podcasting_blogdescription_filter');
add_action('rss2_head', 'podcasting_add_itunes_feed');
add_filter('rss_enclosure', 'podcasting_remove_enclosures');
add_action('rss2_item', 'podcasting_add_itunes_item');


/* ------------------------------------ INSTALL ------------------------------------ */

// Install the base podcasting settings
function podcasting_install() {
	// Taxonomy
	wp_insert_term('Default Format', 'podcast_format');
	
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
	add_option('pod_formats', '', 'Explict settings for podcast formats');
}

// Run on WordPress load
function podcasting_init() {
	add_feed('podcast', 'do_feed_podcast');
	register_taxonomy('podcast_format', 'custom_field');
}

// Add Podcasting to the options menu
function add_podcasting_pages() {
	add_options_page('Podcasting Options', 'Podcasting', 8, basename(__FILE__), 'podcasting_options_page');
}


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
		update_option('pod_title', $_POST['pod_title']);
		update_option('pod_tagline', $_POST['pod_tagline']);
		
		// Update the iTunes options
		update_option('pod_itunes_summary', $_POST['pod_itunes_summary']);
		update_option('pod_itunes_author', $_POST['pod_itunes_author']);
		update_option('pod_itunes_image', podcasting_urlencode($_POST['pod_itunes_image']));
		update_option('pod_itunes_cat1', $_POST['pod_itunes_cat1']);
		update_option('pod_itunes_cat2', $_POST['pod_itunes_cat2']);
		update_option('pod_itunes_cat3', $_POST['pod_itunes_cat3']);
		update_option('pod_itunes_keywords', $_POST['pod_itunes_keywords']);
		update_option('pod_itunes_explicit', $_POST['pod_itunes_explicit']);
		update_option('pod_itunes_ownername', $_POST['pod_itunes_ownername']);
		update_option('pod_itunes_owneremail', $_POST['pod_itunes_owneremail']);
		
		// Add a new format
		if ( isset($_POST['pod_format_name']) ) {
			$args = ( '' != $_POST['pod_format_slug'] ) ? array('slug' => $_POST['pod_format_slug']) : '';
			$format = wp_insert_term($_POST['pod_format_name'], 'podcast_format', $args);
			print_r($format);
			$format = get_term($format['term_id'], 'podcast_format');
			print_r($format);
			
			$pod_explicits = unserialize(get_option('pod_formats'));
			$pod_explicits[$format->slug] = $_POST['pod_format_explicit'];
			print_r($pod_explicits);			
			update_option('pod_formats', serialize($pod_explicits));
		}
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
		
	$pod_formats = get_terms('podcast_format', 'get=all');
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
						<label>Podcast feed address (URL):</label>
					</th>
					<td>
						<p style="margin: 3px 0;"><strong>
							<?php echo get_option('home');
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
						<br />If your podcast's title is different than your blog's title, change the title here.
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="pod_tagline">Podcast tagline:</label>
					</th>
					<td>
						<input type="text" style="width: 95%" name="pod_tagline" id="pod_tagline" value="<?php echo stripslashes(get_option('pod_tagline')); ?>" />
						<br />If your podcast's tagline is different than your blog's tagline, change the tagline here.
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
						<input type="text" size="40" name="pod_itunes_image" id="pod_itunes_image" value="<?php echo rawurldecode(stripslashes(get_option('pod_itunes_image'))); ?>" />
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
				<tr valign="top">
					<th scope="row">
						<label for="pod_itunes_explicit">Explicit:</label>
					</th>
					<td>
						<select name="pod_itunes_explicit" id="pod_itunes_explicit">
							<option value="">No</option>
							<option value="yes"<?php echo ( 'yes' == get_option(pod_itunes_explicit) ) ? ' selected="selected"' : ''; ?>>Yes</option>
							<option value="clean"<?php echo ( 'clean' == get_option(pod_itunes_explicit) ) ? ' selected="selected"' : ''; ?>>Clean</option>
						</select>
						<br />Notifies readers your podcast contains explicit material. Select clean if your podcast removed any explicit content. Note: iTunes requires all explicit podcast to mark themself as one. Failure to do so can result in removal from the iTunes podcast directory.
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="pod_itunes_ownername">Owner Name:</label>
					</th>
					<td>
						<input type="text" size="40" name="pod_itunes_ownername" id="pod_itunes_ownername" value="<?php echo stripslashes(get_option('pod_itunes_ownername')); ?>" />
						<br />Your podcast's owner's name. The owner name will not be publically displayed and is used only by iTunes in the event they need to contact your podcast.
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="pod_itunes_owneremail">Owner E-mail Address:</label>
					</th>
					<td>
						<input type="text" size="40" name="pod_itunes_owneremail" id="pod_itunes_owneremail" value="<?php echo stripslashes(get_option('pod_itunes_owneremail')); ?>" />
						<br />Your podcast's owner's e-mail address. The owner e-mail address will not be publically displayed and is used only by iTunes in the event they need to contact your podcast.
					</td>
				</tr>
			</table>
		</fieldset>
		
		<p class="submit">
			<input type="submit" name="Submit" value="Update Options &raquo;" />
		</p>
		
		<?php if ( count($pod_formats) > 1 ) { ?>
		<fieldset class="options">
			<legend>Formats</legend>
			<?php foreach ($pod_formats as $pod_format) {
			if ( 'default-format' != $pod_format->slug ) {
				if ( $term_count > 0 ) $term_ids .= ','; $term_count++;
				$term_ids .= $pod_format->term_id; ?>
				<table cellpadding="3" class="pod_format">
					<tr>
						<td class="pod-title">Format Feed</td>
						<td colspan="6">
							<input type="text" name="pod_format_feed" class="pod_format_feed" value="<?php
							echo get_option('home');
							global $wp_rewrite;
							if ($wp_rewrite->using_permalinks())
								echo "/feed/podcast/$pod_format->slug/";
							else
								echo "/?feed=podcast&format=$pod_format->slug"; ?>" readonly="readonly" />
						</td>
					</tr>
					<tr>
						<td class="pod-title">Format Name</td>
						<td><input type="text" name="pod_format_name_<?php echo $pod_format->term_id; ?>" class="pod_format_name" value="<?php echo $pod_format->name; ?>" />					
						<td class="pod-title">Format Slug</td>
						<td><input type="text" name="pod_format_slug_<?php echo $pod_format->term_id; ?>" class="pod_format_slug" value="<?php echo $pod_format->slug; ?>" /></td>					
						<td class="pod-title">Explicit</td>
						<td><select name="pod_format_explicit_<?php echo $pod_format->term_id; ?>" class="pod_format_explicit">
							<?php $explicits = array('', 'no', 'yes', 'clean');
							$format_explicit = unserialize(get_option('pod_formats'));
							foreach ($explicits as $explicit) {
								$selected = ($explicit == $format_explicit[$pod_format->slug]) ? ' selected="selected"' : '';
								echo '<option value="' . $explicit . '"' . $selected . '>' . ucfirst($explicit) . '</option>';
							} ?>
						</select></td>					
						<td class="pod-update">
							<input name="Submit" type="submit" class="" value="Update" /> 
							<input name="delete_pod_format_<?php echo $pod_format->slug; ?>" type="submit" class="" value="Delete" onclick="return deleteSomething( 'podcast_format', , 'You are about to delete a podcast format. All episodes currently assigned to this format will become assigned to no format.\n\'OK\' to delete, \'Cancel\' to stop.' );" /></td>
					</tr>
				</table>
				<input name="term_ids" type="hidden" value="<?php echo $term_ids; ?>" />
			<?php } } ?>
		</fieldset>
		<?php } ?>
		
		<fieldset class="options">
			<legend>Add a New Format</legend>
			<table class="optiontable">
				<tr valign="top">
					<th scope="row">
						<label for="pod_format_name">Format name:</label>
					</th>
					<td>
						<input type="text" size="40" name="pod_format_name" id="pod_format_name" value="" />
						<br />The display name of your new new format.
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="pod_format_slug">Format slug:</label>
					</th>
					<td>
						<input type="text" size="40" name="pod_format_slug" id="pod_format_slug" value="" />
						<br />If you leave this field blank, a slug will automatically be generated for you.
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="pod_format_explicit">Explicit:</label>
					</th>
					<td>
						<select name="pod_format_explicit" id="pod_format_explicit">
							<option value=""></option>
							<option value="no">No</option>
							<option value="yes">Yes</option>
							<option value="clean">Clean</option>
						</select>
						<br />The explicit setting for this format. If you leave this field blank, your global podcast explicit setting will be used.
					</td>
				</tr>
			</table>
		</fieldset>
		
		<p class="submit">
			<input type="submit" name="Submit" value="Add Format &raquo;" />
		</p>
	</div>
	</form>
	
	<?php
} // podcasting_options_page()

// Convert image URL to valid URL
function podcasting_urlencode($url) {
	$url = str_replace('http://', '', $url);
	return 'http://' . implode('/', array_map('rawurlencode', explode('/', $url)));
}


/* ------------------------------------- EDIT -------------------------------------- */

// Required information needed for post form
function podcasting_admin_head() {
	echo '<link rel="stylesheet" href="' . get_option('siteurl') . '/wp-content/plugins/podcasting/podcasting-admin.css" type="text/css" />';
}

// Podcasting post form
function podcasting_edit_form() {
	global $wpdb, $post;
	if ($post->ID)
		$enclosures = $wpdb->get_results("SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE post_id = {$post->ID} AND meta_key = 'enclosure' ORDER BY meta_id", ARRAY_A);
	$pod_formats = get_terms('podcast_format', 'get=all'); ?>
	<div class="dbx-b-ox-wrapper"><fieldset id="podcasting" class="dbx-box">
	<div class="dbx-h-andle-wrapper">
		<h3 class="dbx-handle">Podcasting</h3>
	</div>
	<div class="dbx-c-ontent-wrapper"><div class="dbx-content">
		<?php if ( !empty($enclosures) ) { ?>
		<?php foreach ($enclosures as $enclosure) {
			if ( $enclosure_count > 0 ) $enclosure_ids .= ','; $enclosure_count++;
			$enclosure_ids .= $enclosure['meta_id'];
			$enclosure_value = explode("\n", $enclosure['meta_value']);
			$enclosure_itunes = unserialize($enclosure_value[3]); ?>
			<table cellpadding="3" class="pod_enclosure">
				<tr>
					<td class="pod-title">File</td>
					<td colspan="6"><input type="text" name="pod_file_<?php echo $enclosure['meta_id']; ?>" class="pod_file" value="<?php echo $enclosure_value[0]; ?>" readonly="readonly" /></td>
				</tr>
				<tr>
					<td class="pod-title">Format</td>
					<td><select name="pod_format_<?php echo $enclosure['meta_id']; ?>" class="pod_format">
						<?php foreach ($pod_formats as $pod_format) {
							$selected = ($pod_format->slug == $enclosure_itunes['format']) ? ' selected="selected"' : '';
							echo '<option value="' . $pod_format->slug . '"' . $selected . '>' . $pod_format->name . '</option>';
						} ?>
					</select></td>
					<td class="pod-title"><a href="#">Keywords<span>Up to 12 comma-separated words which iTunes uses for search placement.</span></a></td>
					<td colspan="4"><input type="text" name="pod_keywords_<?php echo $enclosure['meta_id']; ?>" class="pod_keywords" value="<?php echo stripslashes($enclosure_itunes['keywords']); ?>" /></td>
				</tr>
				<tr>
					<td class="pod-title"><a href="#">Author<span>Author name if different than default.</span></a></td>
					<td><input type="text" name="pod_author_<?php echo $enclosure['meta_id']; ?>" class="pod_author" value="<?php echo stripslashes($enclosure_itunes['author']); ?>" /></td>
					<td class="pod-title"><a href="#">Length<span>Length of the podcast in HH:MM:SS format.</span></a></td>
					<td class="pod-length"><input type="text" name="pod_length_<?php echo $enclosure['meta_id']; ?>" class="pod_length" value="<?php echo stripslashes($enclosure_itunes['length']); ?>" /></td>
					<td class="pod-title"><a href="#">Explicit<span>Explicit setting if different than default.</span></a></td>
					<td class="pod-explicit"><select name="pod_explicit_<?php echo $enclosure['meta_id']; ?>" class="pod_format">
						<?php $explicits = array('', 'no', 'yes', 'clean');
						foreach ($explicits as $explicit) {
							$selected = ($explicit == $enclosure_itunes['explicit']) ? ' selected="selected"' : '';
							echo '<option value="' . $explicit . '"' . $selected . '>' . ucfirst($explicit) . '</option>';
						} ?>
					</select></td>
					<td class="pod-update"><input name="save" type="submit" class="" value="Update" /> <input name="delete_pod_<?php echo $enclosure['meta_id']; ?>" type="submit" class="" value="Delete" onclick="return deleteSomething( 'podcast', <?php echo $enclosure['meta_id']; ?>, 'You are about to delete a podcast.\n\'OK\' to delete, \'Cancel\' to stop.' );" /></td>
				</tr>
			</table>
			<input name="enclosure_ids" type="hidden" value="<?php echo $enclosure_ids; ?>" />
		<?php } ?>
		<?php } ?>
		<?php if ($enclosures) { ?>
			<h3>Add a new file:</h3>
		<?php } ?>
		<table cellpadding="3" class="pod_new_enclosure">
			<tr>
				<td class="pod-title">File URL</td>
				<td><input type="text" name="pod_new_file" class="pod_new_file" value="" /></td>
				<td class="pod-new-format"><select name="pod_format" class="pod_new_format">
					<?php foreach ($pod_formats as $pod_format) {
						$selected = ( 'default-format' == $pod_format->slug ) ? 'selected="selected"' : '';
						echo '<option value="' . $pod_format->slug . '"' . $selected . '>' . $pod_format->name . '</option>';
					} ?>
				</select></td>
				<td class="submit"><input name="save" type="submit" class="" value="Add" /></td>
			</tr>
		</table>
	</div></div>
	</fieldset></div>
<?php } // podcasting_edit_form()

// Save post form
function podcasting_save_form($postID) {
	global $wpdb;
	
	// Security prevention
	if ( !current_user_can('edit_post', $postID) )
		return $postID;

	// Extra security prevention
	if (isset($_POST['comment_post_ID'])) return $postID;
	if (isset($_POST['not_spam'])) return $postID; // akismet fix
	if (isset($_POST['comment'])) return $postID; // moderation.php fix

	// Update enclosures
	$enclosure_ids = explode(',', $_POST['enclosure_ids']);
	$enclosures = get_post_meta($postID, 'enclosure'); $i = 0;
	foreach ($enclosure_ids as $enclosure_id) {
		// Ensure we're dealing with an ID
		$enclosure_id = (int) $enclosure_id;
		
		$itunes = serialize(array(
			'format' => $_POST['pod_format_' . $enclosure_id],
			'keywords' => $_POST['pod_keywords_' . $enclosure_id],
			'author' => $_POST['pod_author_' . $enclosure_id],
			'length' => $_POST['pod_length_' . $enclosure_id],
			'explicit' => $_POST['pod_explicit_' . $enclosure_id]
			));
		
		// Update format
		wp_set_object_terms($enclosure_id, $_POST['pod_format_' . $enclosure_id], 'podcast_format', false);
		
		// Update enclsoure
		$enclosure = explode("\n", $enclosures[$i]);
		$enclosure[3] = $itunes;
		update_post_meta($postID, 'enclosure', implode("\n", $enclosure), $enclosures[$i]);
		$i++;
		
		// Delete enclosure
		if (isset($_POST['delete_pod_' . $enclosure_id])) {
			// Remove format
			wp_delete_object_term_relationships($enclosure_id, 'podcast_format');
			// Remove enclosure
			delete_meta($enclosure_id);
			// Fake a save
			$_POST['save'] = 'Update';
		}
	}
	
	// Add new enclosures
	if ( (isset($_POST['pod_new_file'])) && ('' != $_POST['pod_new_file']) ) {
		$content = $_POST['pod_new_file'];
		$enclosed = get_enclosed($postID);
		do_enclose($content, $postID);
		
		// Add relationship if new enclosure
		if ( !in_array($content, $enclosed) ) {
			$enclosure_id = $wpdb->get_var("SELECT meta_id FROM {$wpdb->postmeta} WHERE post_id = {$postID} AND meta_key = 'enclosure' ORDER BY meta_id DESC"); // Find the enclosure we just added
			wp_set_object_terms($enclosure_id, 'default-format', 'podcast_format', false);
		}		
	}
	
	return $postID;
} // podcasting_save_form()

// Cleanup a deleted post
function podcasting_delete_form($postID) {
	$enclosure_ids = explode(',', $_POST['enclosure_ids']);
	foreach ($enclosure_ids as $enclosure_id) {
		$enclosure_id = (int) $enclosure_id;
		wp_delete_object_term_relationships($enclosure_id, 'podcast_format');
	}
	return $postID;
}


/* ------------------------------------- WORK -------------------------------------- */

// Create a custom feed
function do_feed_podcast() {
	global $wp_query;
	$wp_query->get_posts();
	load_template(ABSPATH . 'wp-rss2.php');
}

// Pretty permalinks for the custom feed
function podcasting_rewrite_rules($wp_rewrite) {
	// Rewrite rules are manually entered as there is no hook for adding addition feed queries
	$feed_rules = array(
		'podcast/(.+)/?$' => 'index.php?feed=podcast&format=' . $wp_rewrite->preg_index(1),
		'feed/podcast/(.+)/?$' => 'index.php?feed=podcast&format=' . $wp_rewrite->preg_index(1),
		'search/(.+)/podcast/(.+)/?$' => 'index.php?s=' . $wp_rewrite->preg_index(1) . '&feed=podcast&format=' . $wp_rewrite->preg_index(2),
		'search/(.+)/feed/podcast/(.+)/?$' => 'index.php?s=' . $wp_rewrite->preg_index(1) . '&feed=podcast&format=' . $wp_rewrite->preg_index(2),
		'category/(.+?)/podcast/(.+)/?$' => 'index.php?category_name=' . $wp_rewrite->preg_index(1) . '&feed=podcast&format=' . $wp_rewrite->preg_index(2),
		'category/(.+?)/feed/podcast/(.+)/?$' => 'index.php?category_name=' . $wp_rewrite->preg_index(1) . '&feed=podcast&format=' . $wp_rewrite->preg_index(2),
		'tag/(.+?)/podcast/(.+)/?$' => 'index.php?tag=' . $wp_rewrite->preg_index(1) . '&feed=podcast&format=' . $wp_rewrite->preg_index(2),
		'tag/(.+?)/feed/podcast/(.+)/?$' => 'index.php?tag=' . $wp_rewrite->preg_index(1) . '&feed=podcast&format=' . $wp_rewrite->preg_index(2),
		'author/([^/]+)/podcast/(.+)/?$' => 'index.php?author_name=' . $wp_rewrite->preg_index(1) . '&feed=podcast&format=' . $wp_rewrite->preg_index(2),
		'author/([^/]+)/feed/podcast/(.+)/?$' => 'index.php?author_name=' . $wp_rewrite->preg_index(1) . '&feed=podcast&format=' . $wp_rewrite->preg_index(2),
		'([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/podcast/(.+)/?$' => 'index.php?year=' . $wp_rewrite->preg_index(1) . '&monthnum=' . $wp_rewrite->preg_index(2) . '&day=' . $wp_rewrite->preg_index(3) . '&feed=podcast&format=' . $wp_rewrite->preg_index(4),
		'([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/podcast/(.+)/?$' => 'index.php?year=' . $wp_rewrite->preg_index(1) . '&monthnum=' . $wp_rewrite->preg_index(2) . '&day=' . $wp_rewrite->preg_index(3) . '&feed=podcast&format=' . $wp_rewrite->preg_index(4),
		'([0-9]{4})/([0-9]{1,2})/podcast/(.+)/?$' => 'index.php?year=' . $wp_rewrite->preg_index(1) . '&monthnum=' . $wp_rewrite->preg_index(2) . '&feed=podcast&format=' . $wp_rewrite->preg_index(3),
		'([0-9]{4})/([0-9]{1,2})/feed/podcast/(.+)/?$' => 'index.php?year=' . $wp_rewrite->preg_index(1) . '&monthnum=' . $wp_rewrite->preg_index(2) . '&feed=podcast&format=' . $wp_rewrite->preg_index(3),
		'([0-9]{4})/podcast/(.+)/?$' => 'index.php?year=' . $wp_rewrite->preg_index(1) . '&feed=podcast&format=' . $wp_rewrite->preg_index(2),
		'([0-9]{4})/feed/podcast/(.+)/?$' => 'index.php?year=' . $wp_rewrite->preg_index(1) . '&feed=podcast&format=' . $wp_rewrite->preg_index(2),
		'([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/podcast/(.+)/?$' => 'index.php?year=' . $wp_rewrite->preg_index(1) . '&monthnum=' . $wp_rewrite->preg_index(2) . '&day=' . $wp_rewrite->preg_index(3) . '&name=' . $wp_rewrite->preg_index(4) . '&feed=podcast&format=' . $wp_rewrite->preg_index(5),
		'([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/feed/podcast/(.+)/?$' => 'index.php?year=' . $wp_rewrite->preg_index(1) . '&monthnum=' . $wp_rewrite->preg_index(2) . '&day=' . $wp_rewrite->preg_index(3) . '&name=' . $wp_rewrite->preg_index(4) . '&feed=podcast&format=' . $wp_rewrite->preg_index(5)
	);

	$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
} // podcasting_rewrite_rules()

// Adds variable to select format for podcasting
function podcasting_query_vars($vars) {
	$vars[] = 'format';
	return $vars;
}

// Add the join needed for enclosures only
function podcasting_feed_join($join) {
	global $wpdb;
	if ( 'podcast' == get_query_var('feed') ) {		
		$join .= " INNER JOIN {$wpdb->postmeta} pod_meta ON {$wpdb->posts}.ID = pod_meta.post_id";		
		$join .= " INNER JOIN {$wpdb->term_relationships} pod_rel ON (pod_meta.meta_id = pod_rel.object_id)";		
		$join .= " INNER JOIN {$wpdb->term_taxonomy} pod_tax ON (pod_rel.term_taxonomy_id = pod_tax.term_taxonomy_id)";
		$join .= " INNER JOIN {$wpdb->terms} pod_terms ON (pod_tax.term_id = pod_terms.term_id)";
	}
	return $join;
}

// Add the where needed for enclosures only
function podcasting_feed_where($where) {
	global $wpdb;
	if ( 'podcast' == get_query_var('feed') ) {
		$podcast_format = ( '' == get_query_var('format') ) ? 'default-format' : get_query_var('format');
		
		$where .= " AND pod_meta.meta_key = 'enclosure'";
		$where .= " AND pod_terms.slug = '{$podcast_format}'";
	}
	return $where;
}

// Add the groupby needed for enclosures only
function podcasting_feed_groupby($groupby) {
	global $wpdb;
	if ( 'podcast' == get_query_var('feed') )
		$groupby = "{$wpdb->posts}.ID";
	return $groupby;
}

// Add the feed autodiscovery links to <head> section
function podcasting_add_feed_discovery() {
	global $wp_rewrite;
	$podcast_url = ($wp_rewrite->using_permalinks()) ? '/feed/podcast/' : '/?feed=podcast';
	$podcast_url = get_option('home') . $podcast_url;
	echo '	<link rel="alternate" type="application/rss+xml" title="Podcast: ' . htmlentities(stripslashes(get_option('pod_title'))) . '" href="' . $podcast_url . '" />' . "\n";
}

// Add the iTunes xml information
function podcasting_add_itunes_xml() {
	if ( 'podcast' == get_query_var('feed') ) {
		echo 'xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"';
	}
}

// Change the podcast title
function podcasting_blogname_filter($title) {
	if ( 'podcast' == get_query_var('feed') ) {
		$podcast_format = get_term_by('slug', get_query_var('format'), 'podcast_format');
		$title = stripslashes(get_option('pod_title'));
		if ( 'default-format' != get_query_var('format') && '' != get_query_var('format') && !empty($podcast_format) )
			$title .= " ($podcast_format->name)";
	}
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
			echo '<itunes:summary>' . htmlentities(stripslashes(get_option('pod_itunes_summary'))) . '</itunes:summary>' . "\n	";
		// iTunes subtitle
		if ( '' != get_option('pod_tagline') )
			echo '<itunes:subtitle>' . htmlentities(stripslashes(get_option('pod_tagline'))) . '</itunes:subtitle>' . "\n	";
		// iTunes author
		if ( '' != get_option('pod_itunes_author') )
			echo '<itunes:author>' . htmlentities(stripslashes(get_option('pod_itunes_author'))) . '</itunes:author>' . "\n	";
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
			echo '<itunes:keywords>' . htmlentities(stripslashes(get_option('pod_itunes_keywords'))) . '</itunes:keywords>' . "\n	";
		// iTunes keywords
		if ( '' != get_option('pod_itunes_explicit') )
			echo '<itunes:explicit>' . get_option('pod_itunes_explicit') . '</itunes:explicit>' . "\n	";
		else
			echo '<itunes:explicit>no</itunes:explicit>' . "\n	";
		// iTunes owner information
		if ( ( '' != get_option('pod_itunes_ownername') ) || ( '' != get_option('pod_itunes_owneremail') ) ) {
			echo '<itunes:owner>' . "\n	";
			if ( '' != get_option('pod_itunes_ownername') )
				echo '	<itunes:name>' . htmlentities(stripslashes(get_option('pod_itunes_ownername'))) . '</itunes:name>' . "\n	";
			if ( '' != get_option('pod_itunes_owneremail') )
				echo '	<itunes:email>' . htmlentities(stripslashes(get_option('pod_itunes_owneremail'))) . '</itunes:email>' . "\n	";
			echo '</itunes:owner>' . "\n	";
		}
	}
} // podcasting_add_itunes_feed()

// Only enclosures of the current format
function podcasting_remove_enclosures($enclosure) {
	if ( 'podcast' == get_query_var('feed') ) {
		$podcast_format = ( '' == get_query_var('format') ) ? 'default-format' : get_query_var('format');
		$enclosures = get_post_custom_values('enclosure');
		$podcast_urlformats = array();
	
		// Check if the enclosure should be displayed
		foreach ($enclosures as $enclose) {
			$enclose = explode("\n", $enclose);
			$enclosure_itunes = unserialize($enclose[3]);
			$enclosure_url = explode('"', $enclosure);
			if ( ( $enclosure_url[1] == trim(htmlspecialchars($enclose[0])) ) && ( $enclosure_itunes['format'] == $podcast_format ) )
				return $enclosure;
		}
	}
}

// Add the special iTunes information to item
function podcasting_add_itunes_item() {
	if ( 'podcast' == get_query_var('feed') ) {
		$podcast_format = ( '' == get_query_var('format') ) ? 'default-format' : get_query_var('format');
		$enclosures = get_post_custom_values('enclosure');
		foreach ($enclosures as $enclosure) {
			$enclosure_itunes = explode("\n", $enclosure);
			$enclosure_itunes = unserialize($enclosure_itunes[3]);
			if ($enclosure_itunes['format'] == $podcast_format) break;
		}
		
		// iTunes summary
		ob_start(); the_content(); $itunes_summary = ob_get_contents(); ob_end_clean();
		echo '<itunes:summary>' . htmlentities(strip_tags(stripslashes($itunes_summary))) . '</itunes:summary>' . "\n";
		// iTunes subtitle
		ob_start(); the_excerpt_rss(); $itunes_subtitle = ob_get_contents(); ob_end_clean();
		echo '<itunes:subtitle>' . htmlentities(strip_tags(stripslashes($itunes_subtitle))) . '</itunes:subtitle>' . "\n";
		// iTunes author
		if ( '' != $enclosure_itunes['author'] )
			echo '<itunes:author>' . htmlentities(stripslashes($enclosure_itunes['author'])) . '</itunes:author>' . "\n";
		// iTunes duration
		if ( '' != $enclosure_itunes['length'] )
			echo '<itunes:duration>' . htmlentities(stripslashes($enclosure_itunes['length'])) . '</itunes:duration>' . "\n";
		// iTunes keywords
		if ( '' != $enclosure_itunes['keywords'] )
			echo '<itunes:keywords>' . htmlentities(stripslashes($enclosure_itunes['keywords'])) . '</itunes:keywords>' . "\n";
		// iTunes explicit
		if ( '' != $enclosure_itunes['explicit'] )
			echo '<itunes:explicit>' . $enclosure_itunes['explicit'] . '</itunes:explicit>' . "\n";
	}
} // podcasting_add_itunes_item()

?>