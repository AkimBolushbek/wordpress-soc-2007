<?php
/*
Plugin Name: Easier Template Tags
Plugin URI: http://wordpress-soc-2007.googlecode.com/svn/trunk/zooplah/
Description: Provides an editor that allows you to easily edit themes without seeing much of the underlying PHP code.
Version: <a href="http://en.wikipedia.org/wiki/Elliot_Carver">Carver</a>
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

require_once 'includes/eztags-functions-public.php';

function add_eztags_admin_pages()
{
	add_theme_page(_z('Easier Template Tags'), _z('Easier Theme Editor'), edit_themes, get_eztags_dir() . 'pages/eztags-subpanel.php');
}

add_action('admin_menu', 'add_eztags_admin_pages');

?>
