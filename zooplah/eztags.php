<?php
/*
Plugin Name: Easier Template Tags
Plugin URI: http://wordpress-soc-2007.googlecode.com/svn/trunk/zooplah/
Description: Converts Blogger-style template tags to WordPress template tags
Version: <a href="http://en.wikipedia.org/wiki/Ernst_Stavro_Blofeld">Blofeld</a>
Author: Keith Bowes
Author URI: http://zooplah.farvista.net/
*/

class EzTags
{
	function processTemplate()
	{
		$tpl = get_option('template');
		return $tpl;
	}
}


add_filter('template', array(EzTags, 'processTemplate'));

?>
