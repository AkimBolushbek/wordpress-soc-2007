<?php
/*
Plugin Name: Easier Template Tags
Plugin URI: http://wordpress-soc-2007.googlecode.com/svn/trunk/zooplah/
Description: Converts Blogger-style template tags to WordPress template tags
Version: <a href="http://en.wikipedia.org/wiki/Sir_Hugo_Drax">Drax</a>
Author: Keith Bowes
Author URI: http://zooplah.farvista.net/
*/

require_once ABSPATH . WPINC . '/pluggable.php';

require_once ABSPATH . '/wp-admin/admin-functions.php';
require_once ABSPATH . '/wp-admin/admin.php';
require_once ABSPATH . '/wp-admin/menu.php';

add_theme_page('Easier Template Tags', 'Easier Theme Editor', edit_themes, 'eztagspanel.php');

?>
