<?php
/*
Plugin Name: Thesis OpenHook
Plugin URI: http://rickbeckman.com/thesis-openhook/
Description: This plugin allows you to insert arbitrary content into the many hooks that the Thesis Theme Framework provides. Never again edit a file! Based on <a href="http://xentek.net/code/wordpress/plugins/k2-hook-up/">K2 Hook Up</a> and GPLed.
Version: 1.1
Author: Rick Beckman
Author URI: http://rickbeckman.com/

Copyright 2008  Eric Marden  (email : wp@xentek.net)

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

add_action('admin_menu', 'add_openhook_options_page');

/* Thesis Hooks */
add_action('thesis_hook_before_html', 'openhook_before_html');
add_action('thesis_hook_after_html', 'openhook_after_html');
add_action('thesis_hook_before_header', 'openhook_before_header');
add_action('thesis_hook_after_header', 'openhook_after_header');
add_action('thesis_hook_header', 'openhook_header');
add_action('thesis_hook_before_title', 'openhook_before_title');
add_action('thesis_hook_after_title', 'openhook_after_title');
add_action('thesis_hook_before_content', 'openhook_before_content');
add_action('thesis_hook_after_content', 'openhook_after_content');
add_action('thesis_hook_before_post', 'openhook_before_post');
add_action('thesis_hook_after_post', 'openhook_after_post');
add_action('thesis_hook_before_headline', 'openhook_before_headline');
add_action('thesis_hook_after_headline', 'openhook_after_headline');
add_action('thesis_hook_byline_item', 'openhook_byline_item');
add_action('thesis_hook_before_comment_meta', 'openhook_before_comment_meta');
add_action('thesis_hook_after_comment_meta', 'openhook_after_comment_meta');
add_action('thesis_hook_after_comment', 'openhook_after_comment');
add_action('thesis_hook_comment_form', 'openhook_comment_form');
add_action('thesis_hook_archives_template', 'openhook_archives_template');
add_action('thesis_hook_custom_template', 'openhook_custom_template');
add_action('thesis_hook_archive_info', 'openhook_archive_info');
add_action('thesis_hook_404_title', 'openhook_404_title');
add_action('thesis_hook_404_content', 'openhook_404_content');
add_action('thesis_hook_before_sidebars', 'openhook_before_sidebars');
add_action('thesis_hook_after_sidebars', 'openhook_after_sidebars');
add_action('thesis_hook_multimedia_box', 'openhook_multimedia_box');
add_action('thesis_hook_after_multimedia_box', 'openhook_after_multimedia_box');
add_action('thesis_hook_before_sidebar_1', 'openhook_before_sidebar_1');
add_action('thesis_hook_after_sidebar_1', 'openhook_after_sidebar_1');
add_action('thesis_hook_before_sidebar_2', 'openhook_before_sidebar_2');
add_action('thesis_hook_after_sidebar_2', 'openhook_after_sidebar_2');
add_action('thesis_hook_before_footer', 'openhook_before_footer');
add_action('thesis_hook_after_footer', 'openhook_after_footer');
add_action('thesis_hook_footer', 'openhook_footer');

function openhook_remove_actions() {
	if (get_option('openhook_after_html_footer_scripts'))
		remove_action('thesis_hook_after_html', 'thesis_footer_scripts');
	if (get_option('openhook_after_html_ie_clear'))
		remove_action('thesis_hook_after_html', 'thesis_ie_clear');
	if (get_option('openhook_before_header_nav_menu'))
		remove_action('thesis_hook_before_header', 'thesis_nav_menu');
	if (get_option('openhook_header_default_header'))
		remove_action('thesis_hook_header', 'thesis_default_header');
	if (get_option('openhook_after_post_trackback_rdf'))
		remove_action('thesis_hook_after_post', 'thesis_trackback_rdf');
	if (get_option('openhook_after_post_post_tags'))
		remove_action('thesis_hook_after_post', 'thesis_post_tags');
	if (get_option('openhook_after_post_comments_link'))
		remove_action('thesis_hook_after_post', 'thesis_comments_link');
	if (get_option('openhook_after_content_post_navigation'))
		remove_action('thesis_hook_after_content', 'thesis_post_navigation');
	if (get_option('openhook_after_content_prev_next_posts'))
		remove_action('thesis_hook_after_content', 'thesis_prev_next_posts');
	if (get_option('openhook_comment_form_show_subscription_checkbox'))
		remove_action('thesis_hook_comment_form', 'show_subscription_checkbox');
	if (get_option('openhook_archive_info_default_archive_info'))
		remove_action('thesis_hook_archive_info', 'thesis_default_archive_info');
	if (get_option('openhook_archives_template_archives_template'))
		remove_action('thesis_hook_archives_template', 'thesis_archives_template');
	if (get_option('openhook_custom_template_custom_template_sample'))
		remove_action('thesis_hook_custom_template', 'thesis_custom_template_sample');
	if (get_option('openhook_404_title_404_title'))
		remove_action('thesis_hook_404_title', 'thesis_404_title');
	if (get_option('openhook_404_content_404_content'))
		remove_action('thesis_hook_404_content', 'thesis_404_content');
	if (get_option('openhook_footer_thesis_attribution'))
		remove_action('thesis_hook_footer', 'thesis_attribution');
	if (get_option('openhook_footer_admin_link'))
		remove_action('thesis_hook_footer', 'thesis_admin_link');
} 
add_action('wp_head', 'openhook_remove_actions');

function add_openhook_options_page() {
	if (function_exists('add_options_page')) {
		add_theme_page('Thesis OpenHook', 'Thesis OpenHook', 10, dirname(__FILE__) . '/options.php');
	}
}

function openhook_before_html() {
	$val = stripslashes(get_option('openhook_before_html'));

	if (get_option('openhook_before_html_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_html() {
	$val = stripslashes(get_option('openhook_after_html'));
	if (get_option('openhook_after_html_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_before_header() {
	$val = stripslashes(get_option('openhook_before_header'));
	if (get_option('openhook_before_header_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_header() {
	$val = stripslashes(get_option('openhook_after_header'));
	if (get_option('openhook_after_header_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_header() {
	$val = stripslashes(get_option('openhook_header'));
	if (get_option('openhook_header_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_before_title() {
	$val = stripslashes(get_option('openhook_before_title'));
	if (get_option('openhook_before_title_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_title() {
	$val = stripslashes(get_option('openhook_after_title'));
	if (get_option('openhook_after_title_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_before_content() {
	$val = stripslashes(get_option('openhook_before_content'));
	if (get_option('openhook_before_content_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_content() {
	$val = stripslashes(get_option('openhook_after_content'));
	if (get_option('openhook_after_content_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_before_post() {
	$val = stripslashes(get_option('openhook_before_post'));
	if (get_option('openhook_before_post_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_post() {
	$val = stripslashes(get_option('openhook_after_post'));
	if (get_option('openhook_after_post_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_before_headline() {
	$val = stripslashes(get_option('openhook_before_headline'));
	if (get_option('openhook_before_headline_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_headline() {
	$val = stripslashes(get_option('openhook_after_headline'));
	if (get_option('openhook_after_headline_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_byline_item() {
	$val = stripslashes(get_option('openhook_byline_item'));
	if (get_option('openhook_byline_item_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_before_comment_meta() {
	$val = stripslashes(get_option('openhook_before_comment_meta'));
	if (get_option('openhook_before_comment_meta_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_comment_meta() {
	$val = stripslashes(get_option('openhook_after_comment_meta'));
	if (get_option('openhook_after_comment_meta_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_comment() {
	$val = stripslashes(get_option('openhook_after_comment'));
	if (get_option('openhook_after_comment_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_comment_form() {
	$val = stripslashes(get_option('openhook_comment_form'));
	if (get_option('openhook_comment_form_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_archives_template() {
	$val = stripslashes(get_option('openhook_archives_template'));
	if (get_option('openhook_archives_template_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_custom_template() {
	$val = stripslashes(get_option('openhook_custom_template'));
	if (get_option('openhook_custom_template_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_archive_info() {
	$val = stripslashes(get_option('openhook_archive_info'));
	if (get_option('openhook_archive_info_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_404_title() {
	$val = stripslashes(get_option('openhook_404_title'));
	if (get_option('openhook_404_title_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_404_content() {
	$val = stripslashes(get_option('openhook_404_content'));
	if (get_option('openhook_404_content_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_before_sidebars() {
	$val = stripslashes(get_option('openhook_before_sidebars'));
	if (get_option('openhook_before_sidebars_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_sidebars() {
	$val = stripslashes(get_option('openhook_after_sidebars'));
	if (get_option('openhook_after_sidebars_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_multimedia_box() {
	$val = stripslashes(get_option('openhook_multimedia_box'));
	if (get_option('openhook_multimedia_box_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_multimedia_box() {
	$val = stripslashes(get_option('openhook_after_multimedia_box'));
	if (get_option('openhook_after_multimedia_box_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_before_sidebar_1() {
	$val = stripslashes(get_option('openhook_before_sidebar_1'));
	if (get_option('openhook_before_sidebar_1_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_sidebar_1() {
	$val = stripslashes(get_option('openhook_after_sidebar_1'));
	if (get_option('openhook_after_sidebar_1_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_before_sidebar_2() {
	$val = stripslashes(get_option('openhook_before_sidebar_2'));
	if (get_option('openhook_before_sidebar_2_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_sidebar_2() {
	$val = stripslashes(get_option('openhook_after_sidebar_2'));
	if (get_option('openhook_after_sidebar_2_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_before_footer() {
	$val = stripslashes(get_option('openhook_before_footer'));
	if (get_option('openhook_before_footer_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_after_footer() {
	$val = stripslashes(get_option('openhook_after_footer'));
	if (get_option('openhook_after_footer_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
}

function openhook_footer() {
	$val = stripslashes(get_option('openhook_footer'));
	if (get_option('openhook_footer_php')) {
		ob_start();
		eval("?>$val<?php ");
		$val = ob_get_contents();
		ob_end_clean();
	}
	echo $val;
	
	if (get_option('openhook_footer_debug_info') && current_user_can('level_10')) {
		echo '<p>', get_num_queries(), __(' queries. ');
		timer_stop(1);
		_e('seconds.');
		echo '</p>';
	}

}

?>