<?php
/*
Plugin Name: Easier Template Tags
Plugin URI: http://wordpress-soc-2007.googlecode.com/svn/trunk/zooplah/
Description: Converts Blogger-style template tags to WordPress template tags
Version: <a href="http://en.wikipedia.org/wiki/Ernst_Stavro_Blofeld">Blofeld</a>
Author: Keith Bowes
Author URI: http://zooplah.farvista.net/
*/

$template_name = '';

class EzTags
{
	var $_template;
	var $_template_home_file;
	function processTemplate()
	{
		global $template_name;
		if ( $template_name !== '' ) {
			update_option('template', 'zooplah');
			//return null;
		}
	        $template_name = get_option('template');
		$_template = ABSPATH . 'wp-content/themes/' . $template_name;
		if (file_exists($_template . '/index.php')) $_template_home_file = $_template . '/index.php';
		else if (file_exists($_template . '/home.php')) $_template_home_file = $_template . '/home.php';
		$contents = file_get_contents($_template_home_file);
		echo $contents;
		return null;
	}
}


add_filter('template', array(EzTags, 'processTemplate'));

?>
