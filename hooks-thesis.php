<?php

/**
 * Prevent direct access to this file
 */
if ( ! function_exists( 'add_action' ) ) {
	@header( 'HTTP/1.1 403 Forbidden' );
	@header( 'Status: 403 Forbidden' );
	@header( 'Connection: Close' );
	@exit;
} 

/**
 * An array of available WordPress hooks
 */
function openhook_thesis_hooks() {
	$thesis_hooks = array(
		'thesis_hook_before_html' => array(
			'name' => 'thesis_hook_before_html',
		),
		'thesis_hook_after_html' => array(
			'name' => 'thesis_hook_after_html',
		),
		'thesis_hook_before_header' => array(
			'name' => 'thesis_hook_before_header',
			'unhook' => array(
				'thesis_nav_menu' => array(
					'name' => 'thesis_nav_menu',
					'desc' => __( 'The default navigation menu', 'openhook' ),
				),
			),
		),
		'thesis_hook_after_header' => array(
			'name' => 'thesis_hook_after_header',
		),
		'thesis_hook_header' => array(
			'name' => 'thesis_hook_header',
			'unhook' => array(
				'thesis_default_header' => array(
					'name' => 'thesis_default_header',
					'desc' => __( 'Default header', 'openhook' ),
				),
			),
		),
		'thesis_hook_before_title' => array(
			'name' => 'thesis_hook_before_title',
		),
		'thesis_hook_after_title' => array(
			'name' => 'thesis_hook_after_title',
		),
		'thesis_hook_first_nav_item' => array(
			'name' => 'thesis_hook_first_nav_item',
		),
		'thesis_hook_last_nav_item' => array(
			'name' => 'thesis_hook_last_nav_item',
		),
		'thesis_hook_before_content_box' => array(
			'name' => 'thesis_hook_before_content_box',
		),
		'thesis_hook_after_content_box' => array(
			'name' => 'thesis_hook_after_content_box',
		),
		'thesis_hook_content_box_top' => array(
			'name' => 'thesis_hook_content_box_top',
		),
		'thesis_hook_content_box_bottom' => array(
			'name' => 'thesis_hook_content_box_bottom',
		),
		'thesis_hook_before_content' => array(
			'name' => 'thesis_hook_before_content',
		),
		'thesis_hook_after_content' => array(
			'name' => 'thesis_hook_after_content',
			'unhook' => array(
				'thesis_post_navigation' => array(
					'name' => 'thesis_post_navigation',
					'desc' => __( 'Displays links to the next or previous post', 'openhook' ),
				),
				'thesis_prev_next_posts' => array(
					'name' => 'thesis_prev_next_posts',
					'desc' => __( 'Displays links to the next or previous pages of an archive listing', 'openhook' ),
				),
			),
		),
		'thesis_hook_before_content_area' => array(
			'name' => 'thesis_hook_before_content_area',
		),
		'thesis_hook_after_content_area' => array(
			'name' => 'thesis_hook_after_content_area',
		),
		'thesis_hook_feature_box' => array(
			'name' => 'thesis_hook_feature_box',
		),
		'thesis_hook_before_post_box' => array(
			'name' => 'thesis_hook_before_post_box',
		),
		'thesis_hook_after_content_box' => array(
			'name' => 'thesis_hook_after_content_box',
		),
		'thesis_hook_post_box_top' => array(
			'name' => 'thesis_hook_post_box_top',
		),
		'thesis_hook_post_box_bottom' => array(
			'name' => 'thesis_hook_post_box_bottom',
		),
		'thesis_hook_before_teasers_box' => array(
			'name' => 'thesis_hook_before_teasers_box',
		),
		'thesis_hook_after_teasers_box' => array(
			'name' => 'thesis_hook_after_teasers_box',
		),
		'thesis_hook_before_post' => array(
			'name' => 'thesis_hook_before_post',
		),
		'thesis_hook_after_post' => array(
			'name' => 'thesis_hook_after_post',
			'unhook' => array(
				'thesis_post_tags' => array(
					'name' => 'thesis_post_tags',
					'desc' => __( 'Displays a list of tags applied to the post', 'openhook' ),
				),
				'thesis_comments_link' => array(
					'name' => 'thesis_comments_link',
					'desc' => __( 'Displays a link to the comments of a post', 'openhook' ),
				),
			),
		),
		'thesis_hook_before_teaser_box' => array(
			'name' => 'thesis_hook_before_teaser_box',
		),
		'thesis_hook_after_teaser_box' => array(
			'name' => 'thesis_hook_after_teaser_box',
		),
		'thesis_hook_before_teaser' => array(
			'name' => 'thesis_hook_before_teaser',
		),
		'thesis_hook_after_teaser' => array(
			'name' => 'thesis_hook_after_teaser',
		),
		'thesis_hook_before_headline' => array(
			'name' => 'thesis_hook_before_headline',
		),
		'thesis_hook_after_headline' => array(
			'name' => 'thesis_hook_after_headline',
		),
		'thesis_hook_before_teaser_headline' => array(
			'name' => 'thesis_hook_before_teaser_headline',
		),
		'thesis_hook_after_teaser_headline' => array(
			'name' => 'thesis_hook_after_teaser_headline',
		),
		'thesis_hook_byline_item' => array(
			'name' => 'thesis_hook_byline_item',
		),
		'thesis_hook_before_comment_meta' => array(
			'name' => 'thesis_hook_before_comment_meta',
		),
		'thesis_hook_after_comment_meta' => array(
			'name' => 'thesis_hook_after_comment_meta',
		),
		'thesis_hook_after_comment' => array(
			'name' => 'thesis_hook_after_comment',
		),
		'thesis_hook_after_comments' => array(
			'name' => 'thesis_hook_after_comments',
		),
		'thesis_hook_comment_form_top' => array(
			'name' => 'thesis_hook_comment_form_top',
		),
		'thesis_hook_comment_field' => array(
			'name' => 'thesis_hook_comment_field',
		),
		'thesis_hook_after_comment_box' => array(
			'name' => 'thesis_hook_after_comment_box',
			'unhook' => array(
				'show_subscription_checkbox' => array(
					'name' => 'show_subscription_checkbox',
					'desc' => __( 'The Subscribe to Comments plugin subscription checkbox', 'openhook' ),
				),
			),
		),
		'thesis_hook_comment_form_bottom' => array(
			'name' => 'thesis_hook_comment_form_bottom',
		),
		'thesis_hook_archives_template' => array(
			'name' => 'thesis_hook_archives_template',
			'unhook' => array(
				'thesis_archives_template' => array(
					'name' => 'thesis_archives_template',
					'desc' => __( 'Default output for the archives template', 'openhook' ),
				),
			),
		),
		'thesis_hook_custom_template' => array(
			'name' => 'thesis_hook_custom_template',
			'unhook' => array(
				'thesis_custom_template_sample' => array(
					'name' => 'thesis_custom_template_sample',
					'desc' => __( 'Default sample output for the custom template', 'openhook' ),
				),
			),
		),
		'thesis_hook_faux_admin' => array(
			'name' => 'thesis_hook_faux_admin',
		),
		'thesis_hook_404_title' => array(
			'name' => 'thesis_hook_404_title',
			'unhook' => array(
				'thesis_404_title' => array(
					'name' => 'thesis_404_title',
					'desc' => __( 'Displays default 404 error page title', 'openhook' ),
				),
			),
		),
		'thesis_hook_404_content' => array(
			'name' => 'thesis_hook_404_content',
			'unhook' => array(
				'thesis_404_content' => array(
					'name' => 'thesis_404_content',
					'desc' => __( 'Displays default 404 error page content', 'openhook' ),
				),
			),
		),
		'thesis_hook_before_sidebars' => array(
			'name' => 'thesis_hook_before_sidebars',
		),
		'thesis_hook_after_sidebars' => array(
			'name' => 'thesis_hook_after_sidebars',
		),
		'thesis_hook_multimedia_box' => array(
			'name' => 'thesis_hook_multimedia_box',
		),
		'thesis_hook_after_multimedia_box' => array(
			'name' => 'thesis_hook_after_multimedia_box',
		),
		'thesis_hook_before_sidebar_1' => array(
			'name' => 'thesis_hook_before_sidebar_1',
		),
		'thesis_hook_after_sidebar_1' => array(
			'name' => 'thesis_hook_after_sidebar_1',
		),
		'thesis_hook_before_sidebar_2' => array(
			'name' => 'thesis_hook_before_sidebar_2',
		),
		'thesis_hook_after_sidebar_2' => array(
			'name' => 'thesis_hook_after_sidebar_2',
		),
		'thesis_hook_before_footer' => array(
			'name' => 'thesis_hook_before_footer',
		),
		'thesis_hook_after_footer' => array(
			'name' => 'thesis_hook_after_footer',
		),
		'thesis_hook_footer' => array(
			'name' => 'thesis_hook_footer',
			'unhook' => array(
				'thesis_attribution' => array(
					'name' => 'thesis_attribution',
					'desc' => __( 'Displays DIYthemes attribution &amp; link', 'openhook' ),
				),
			),
		),
	);

	return $thesis_hooks;
}