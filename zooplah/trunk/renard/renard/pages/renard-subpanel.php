<?php

/* The "Easier Theme Editor" subpanel page
 * It's just a modification of WordPress 2.0's theme editor
 */

require_once '/' . ABSPATH . 'wp-content/plugins/' . get_renard_dir() . '/renard/includes/renard-mediator.php';

$title = __z('Renard Editor');
$parent_file = 'themes.php';

wp_reset_vars(array('action', 'redirect', 'profile', 'error', 'warning', 'a', 'file', 'theme'));

$themes = get_themes();



if (empty($theme)) {
	$theme = get_current_theme();
} else {
	$theme = stripslashes($theme);
 }


if ( ! isset($themes[$theme]) )
	wp_die(__('The requested theme does not exist.'));

$allowed_files = array_merge($themes[$theme]['Stylesheet Files'], $themes[$theme]['Template Files']);

if (empty($file)) {
	$file = $allowed_files[0];
}

$file = validate_file_to_edit($file, $allowed_files);
$real_file = get_real_file_to_edit($file);

$file_show = basename( $file );

?>

<script type="text/javascript">
document.title = '<?php bloginfo(); ?> > <?php echo $title; ?> [<?php echo $file_show; ?>] - WordPress';
</script>

<hr />

<div style="padding: 0.2em 0; text-align: center">
	<a href="<?php bloginfo('url'); ?>/wp-content/plugins/<?php echo get_renard_dir(); ?>renard/docs/readme.html"><?php _ez('README file'); ?></a>
 |
	<a href="<?php bloginfo('url'); ?>/wp-content/plugins/<?php echo get_renard_dir(); ?>renard/docs/tags.txt"><?php _ez('Supported tags'); ?></a>
 |
	<a href="<?php bloginfo('url'); ?>/wp-content/plugins/<?php echo get_renard_dir(); ?>renard/docs/caveats.html"><?php _ez('Caveats'); ?></a>
</div>

<hr />

<?php

switch($action) {

case 'update':

	check_admin_referer('edit-theme_' . $file . $theme);

	if ( !current_user_can('edit_themes') )
		wp_die('<p>'.__('You do not have sufficient permissions to edit templates for this blog.').'</p>');

	$newcontent = stripslashes($_POST['newcontent']);
	$theme = urlencode($theme);
	if (is_writeable($real_file)) {
		$real_file_is_php = strstr($real_file, '.php');
		if ( $real_file_is_php) $newcontent = ez2std($newcontent);

		$f = fopen($real_file, 'w+');
		fwrite($f, $newcontent);
		fclose($f);
		$location = 'themes.php?page=' . get_renard_dir() . "renard/pages/renard-subpanel.php&file=$file&theme=$theme&a=te";
	} else {
		$location = 'themes.php?page=' . get_renard_dir() . "renard/pages/renard-subpanel.php&file=$file&theme=$theme";
	}

	$location = wp_kses_no_null($location);
	$strip = array('%0d', '%0a');
	$location = str_replace($strip, '', $location);
	@header("Location: $location");
	echo "<meta http-equiv='refresh' content='0; $location' />";

	exit();

break;

default:

	if ( !current_user_can('edit_themes') )
		wp_die('<p>'.__('You do not have sufficient permissions to edit themes for this blog.').'</p>');

	require_once('admin-header.php');

	update_recently_edited($file);

	if (!is_file($real_file))
		$error = 1;

	if (!$error && filesize($real_file) > 0) {
		$file_is_php = strstr($real_file, '.php');
		$f = fopen($real_file, 'r');
		$content = fread($f, filesize($real_file));
		$content = htmlspecialchars($content);
	}

	?>
<?php if (isset($_GET['a'])) : ?>
 <div id="message" class="updated fade"><p><?php _e('File edited successfully.') ?></p></div>
<?php endif; ?>
 <div class="wrap">
	 <form name="theme" action="themes.php?page=<?php echo get_renard_dir(); ?>renard/pages/renard-subpanel.php" method="post">
		<?php _e('Select theme to edit:') ?>
		<select name="theme" id="theme">
	<?php
		foreach ($themes as $a_theme) {
		$theme_name = $a_theme['Name'];
		if ($theme_name == $theme) $selected = " selected='selected'";
		else $selected = '';
		$theme_name = attribute_escape($theme_name);
		echo "\n\t<option value=\"$theme_name\" $selected>$theme_name</option>";
	}
?>
 </select>
 <input type="submit" name="Submit" value="<?php _e('Select &raquo;') ?>" class="button" />
 </form>
 </div>

 <div class="wrap"> 
  <?php
	if ( is_writeable($real_file) ) {
		echo '<h2>' . sprintf(__('Editing <code>%s</code>'), $file_show) . '</h2>';
	} else {
		echo '<h2>' . sprintf(__('Browsing <code>%s</code>'), $file_show) . '</h2>';
	}
	?>
	<div id="templateside">
	<h3><?php printf(__("<strong>'%s'</strong> theme files"), $theme) ?></h3>

<?php
if ($allowed_files) :
?>
	<ul>
<?php foreach($allowed_files as $allowed_file) : ?>
<?php if ( strstr($allowed_file, '.php') ) $is_php_file = TRUE;
/* functions.php has the scary PHP stuff.
 * You obviously shouldn't be editing it within this plugin
*/
if ( strstr($allowed_file, 'functions.php') ) $is_php_file = FALSE;

if ( $is_php_file ) $link_color = 'blue';
else $link_color = 'GrayText';

if ( $is_php_file || !get_option('renard_hide_non_php') ) :
?>
		 <li><a href="themes.php?page=<?php echo get_renard_dir(); ?>renard/pages/renard-subpanel.php&file=<?php echo "$allowed_file"; ?>&amp;theme=<?php echo urlencode($theme) ?>" style="color: <?php echo $link_color; ?>; <?php if ( !$is_php_file ) echo 'border-bottom: 0; cursor: auto;'; ?>;"><?php echo get_file_description($allowed_file); ?></a></li>
<?php endif; ?>
<?php  endforeach; ?>
	</ul>
<?php endif; ?>
</div>
	<?php
	if (!$error) {
	?>
		<form name="template" id="template" action="themes.php?page=<?php echo get_renard_dir(); ?>renard/pages/renard-subpanel.php" method="post">
	<?php wp_nonce_field('edit-theme_' . $file . $theme) ?>
		 <div>
<textarea cols="<?php echo (get_option('renard_editor_cols') ? get_option('renard_editor_cols') : 70); ?>" rows="<?php echo (get_option('renard_editor_rows') ? get_option('renard_editor_rows') : 25); ?>" name="newcontent" id="newcontent" tabindex="1"><?php if ($file_is_php) $content = std2ez($content); echo $content; ?></textarea>
		 <input type="hidden" name="action" value="update" />
		 <input type="hidden" name="file" value="<?php echo $file ?>" />
		 <input type="hidden" name="theme" value="<?php echo $theme ?>" />
		 </div>
<?php if ( is_writeable($real_file) ) : ?>
	<p class="submit">
<?php
	echo "<input type='submit' name='submit' value='	" . __('Update File &raquo;') . "' tabindex='2' />";
?>
</p>
<?php else : ?>
<p><em><?php _e('If this file were writable you could edit it.'); ?></em></p>
<?php endif; ?>
	</form>
	<?php
	} else {
		echo '<div class="error"><p>' . __('Oops, no such file exists! Double check the name and try again, merci.') . '</p></div>';
	}
	?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="text-align: right">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
</form>
<div class="clear"> &nbsp; </div>
</div>
<?php
break;
}

?>
