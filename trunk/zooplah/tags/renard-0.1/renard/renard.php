<?php
/*
Plugin Name: Renard
Plugin URI: http://kechjo.cogia.net/blogo/2007/09/09/renard
Description: Provides an editor that allows you to easily edit themes without seeing much of the underlying PHP code.
Version: 0.1
Author: Keith Bowes
Author URI: http://kechjo.cogia.net/
*/

/*  Copyright 2007  Renard

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

require_once 'includes/renard-functions-public.php';

function add_renard_admin_pages()
{
	add_theme_page(__z('Renard Editor'), __z('Renard'), edit_themes, get_renard_dir() . 'renard/pages/renard-subpanel.php');
}

add_action('admin_menu', 'add_renard_admin_pages');

?>
