<?php
/*
Plugin Name: Easier Template Tags
Plugin URI: http://wordpress-soc-2007.googlecode.com/svn/trunk/zooplah/
Description: Converts Blogger-style template tags to WordPress template tags
Version: <a href="http://en.wikipedia.org/wiki/Ernst_Stavro_Blofeld">Blofeld</a>
Author: Keith Bowes
Author URI: http://zooplah.farvista.net/
*/

/* Global vars (necessary evil unless I can figure out a better way
 * to do it) */
$contents = '';

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

	function gtl(){echo "Yeah";$title = get_the_title();if ( empty ($title) ){static $id = 0;$post = &get_post($id);$title = $post->post_title; $id = $post->ID; } return $title;}
	/* Replace the tags with PHP */
	function replace(&$contents)
	{
		$contents = str_replace('<$Loop$>', eval('return "Hello World<br/>\n";'), $contents);
		$contents = str_replace('<$EntryTitle$>', eval('the_author();'), $contents);
	}

	/* Just process the template and fill the content */
	function processTemplate()
	{
		global $contents, $wp_query;

		$template_name = EzTags::getTemplateName();

		if ( is_admin() ) return $template_name;

		$_template = ABSPATH . 'wp-content/themes/' . $template_name;

		if ( file_exists($_template . '/index.php') )
			$_template_home_file = $_template . '/index.php';
		else if ( file_exists($_template . '/home.php') )
			$_template_home_file = $_template . '/home.php';

		if (TRUE) {
		$contents = file_get_contents($_template_home_file);
		$contents = preg_replace('/^(.*)<\?(php)?/', '$1', $contents);
		EzTags::replace($contents);
		eval($contents);}
		else
		{
			ob_start();
			$contents = ob_get_contents();
			ob_end_clean();
			EzTags::replace($contents);
			echo strlen($contents);;
		}

		return NULL;
	}

	/* Ah, small function needed so that we get the right style sheet.
	 * Shouldn't be necessary, I know.  But it is. */
	function getStylesheet()
	{
		$template_name = EzTags::getTemplateName();
		return $template_name;
	}

	/* Get the title */
	function getTitle($a,$b,$c)
	{
		echo 'Getting title...';
		$title = get_the_title();
		echo strlen($title);

		if ( empty($title) )
		{
			$id = 0;
			$post = &get_post($id);
			var_dump($post);
			$title = $post->post_title;
		}

		return $title;

	}
	function init()
	{

	}
}

function _eztags_init($content)
{
	EzTags::replace($content);
	return $content;
}

//error_reporting(E_ERROR|E_PARSE);
add_filter('template', array(EzTags, 'processTemplate'));
add_filter('stylesheet', array(EzTags, 'getStylesheet'));
//add_filter('loop_end', @ob_flush());
//ob_start('_ezTags_init');

?>
