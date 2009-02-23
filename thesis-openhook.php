<?php
/*
Plugin Name: Thesis OpenHook
Plugin URI: http://rickbeckman.org/thesis-openhook/
Description: This plugin allows you to insert arbitrary content into the many hooks that the <a href="http://get-thesis.com/">Thesis Theme Framework</a> provides. Never again edit a file! Based on <a href="http://xentek.net/code/wordpress/plugins/k2-hook-up/">K2 Hook Up</a> and GPLed.
Version: 2.0
Author: Rick Beckman
Author URI: http://rickbeckman.org/

Copyright 2009 Rick Beckman  (email: rick.beckman@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Get the functions which output users' desires.
include('functions-hooks.php');

// Add all of our actions to Thesis' hooks.
include ('actions-add.php');

/**
 * Add nav panel link.
 *
 * Adds the Thesis OpenHook management page to the admin panel
 * navigation menu.
 *
 * @since 1.0
 */
function add_openhook_options_page() {
	if (current_user_can('edit_themes'))
		add_theme_page('Thesis OpenHook', 'Thesis OpenHook', 10, dirname(__FILE__) . '/options.php');
}
// add_action('admin_menu', 'add_openhook_options_page');

?>