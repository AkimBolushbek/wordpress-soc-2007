<?php
/*
Plugin Name: Easier Template Tags
Plugin URI: http://wordpress-soc-2007.googlecode.com/svn/trunk/zooplah/
Description: Converts Blogger-style template tags to WordPress template tags
Version: <a href="http://en.wikipedia.org/wiki/Ernst_Stavro_Blofeld">Blofeld</a>
Author: Keith Bowes
Author URI: http://zooplah.farvista.net/
*/

require_once 'eztagstags.php';

/* The main class for Easier Template Tags */
class EzTags
{
	var $_template;
	var $_template_home_file;

	/* Initialize everything needed to make the system go.
	 * Necessary due to the things I'm overriding. */
	static function initialize()
	{
		require_once ABSPATH . WPINC . '/pluggable.php';
	}

	/* Get the template's name */
	function getTemplateName()
	{
		static $template_name;

		if ( empty($template_name) )
			$template_name = get_option('template');
		else
			update_option('template', $template_name);

		return $template_name;
	}

	/* Replace the tags with PHP */
	function replace(&$contents)
	{
		global $_eztags_entries_loop_begin;
		echo $_eztags_entries_loop_begin;

		$contents = str_replace('<Entries>',  '<?php global $wp_query; var_dump($wp_query); ?>', $contents);
	}

	/* Just process the template and fill the content */
	function processTemplate()
	{
		global $contents;
		if ( $contents != '' ) return;

		$template_name = EzTags::getTemplateName();

		if ( preg_replace('/^(.*)\/$/', '$1', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) != get_option('siteurl') ) return $template_name;

		$_template = ABSPATH . 'wp-content/themes/' . $template_name;

		if ( file_exists($_template . '/index.php') )
			$_template_home_file = $_template . '/index.php';
		else if ( file_exists($_template . '/home.php') )
			$_template_home_file = $_template . '/home.php';

		$contents = file_get_contents($_template_home_file);
		$contents = preg_replace('/^(.*)<\?(php)?/', '$1', $contents);
		EzTags::replace($contents);
		eval($contents);

		return NULL;
	}

	/* Ah, small function needed so that we get the right style sheet.
	 * Shouldn't be necessary, I know.  But it is. */
	function getStylesheet()
	{
		$template_name = EzTags::getTemplateName();
		return $template_name;
	}
}

add_filter('template', array(EzTags, 'processTemplate'));
add_filter('stylesheet', array(EzTags, 'getStylesheet'));

?>
