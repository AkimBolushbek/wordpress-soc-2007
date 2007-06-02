<?php
/*
Plugin Name: Easier Template Tags
Plugin URI: http://wordpress-soc-2007.googlecode.com/svn/trunk/zooplah/
Description: Converts Blogger-style template tags to WordPress template tags
Version: <a href="http://en.wikipedia.org/wiki/Ernst_Stavro_Blofeld">Blofeld</a>
Author: Keith Bowes
Author URI: http://zooplah.farvista.net/
*/

$contents = '';
$template_name = '';

define('TEMPLATEPATH', get_template_directory());;
define('STYLESHEETPATH', get_stylesheet_directory());
include ABSPATH . WPINC . '/locale.php';
$wp_locale =& new WP_Locale();
$wp_query =& new WP_Query();
echo $wp_query == NULL;

class EzTags
{
	var $_template;
	var $_template_home_file;
	function processTemplate()
	{
		global $contents, $template_name;
		if ( empty($template_name) )
			$template_name = get_option('template');
		else
			update_option('template', $template_name);

		if ( is_admin() ) return $template_name;

		$_template = ABSPATH . 'wp-content/themes/' . $template_name;

		if (file_exists($_template . '/index.php')) $_template_home_file = $_template . '/index.php';
		else if (file_exists($_template . '/home.php')) $_template_home_file = $_template . '/home.php';

		$contents = file_get_contents($_template_home_file);
		$contents = preg_replace('/^(.*)<\?(php)?/', '$1', $contents);
		//eval($contents);

		return null;
	}
	function getStylesheet($a)
	{
		global $template_name;
		return $template_name;
	}
	function outputParsed($a)
	{
		global $contents;
		if ( $contents !== '' )
			eval($contents);
		return true;
	}
}


add_filter('template', array(EzTags, 'processTemplate'));
add_filter('stylesheet', array(EzTags, 'getStylesheet'));
add_action('loop_begin', array(EzTags, 'outputParsed'));

?>
