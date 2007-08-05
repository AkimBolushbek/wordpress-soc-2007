<?php
/*
Plugin Name: Easier Template Tags
Plugin URI: http://wordpress-soc-2007.googlecode.com/svn/trunk/zooplah/
Description: Converts Blogger-style template tags to WordPress template tags
Version: <a href="http://en.wikipedia.org/wiki/Auric_Goldfinger">Goldfinger</a>
Author: Keith Bowes
Author URI: http://zooplah.farvista.net/
*/

/*  Copyright 2007  Easier Template Tags

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( is_admin() )
{

	require_once ABSPATH . WPINC . '/pluggable.php';

	require_once ABSPATH . '/wp-admin/admin-functions.php';
	require_once ABSPATH . '/wp-admin/menu.php';

	add_theme_page('Easier Template Tags', 'Easier Theme Editor', edit_themes, 'eztags-subpanel.php');
}

?>