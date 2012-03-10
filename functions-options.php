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
 * Return all hook options to be processed
 *
 * @return array Concatenated list of all relevant options
 * @todo Add theme selecting logic
 */
function openhook_get_relevant_options() {
	$option = get_option( 'openhook_general' );
	$relevant_options = $option[ 'active_actions' ];

	$return = array();

	if ( isset( $relevant_options[ 'openhook_wordpress' ] ) && $relevant_options[ 'openhook_wordpress' ] )
		$return = array_merge( $return, get_option( 'openhook_wordpress' ) );
	if ( isset( $relevant_options[ 'openhook_thesis' ] ) && $relevant_options[ 'openhook_thesis' ] )
		$return = array_merge( $return, get_option( 'openhook_thesis' ) );

	return $return;
}

/**
 * Import Thesis 2 options into Thesis 3
 */
function openhook_import_old_options() {
	if ( current_user_can( 'delete_users' ) && defined( 'OPENHOOK_SAFEGUARD' ) ) {
		$wordpress = array();
		$thesis = array();

		$wordpress[ 'wp_head' ][ 'action' ] = stripslashes( get_option( 'openhook_wp_head' ) );
		$wordpress[ 'wp_head' ][ 'php' ] = get_option( 'openhook_wp_head_php' );
		$wordpress[ 'wp_footer' ][ 'action' ] = stripslashes( get_option( 'openhook_wp_footer' ) );
		$wordpress[ 'wp_footer' ][ 'php' ] = get_option( 'openhook_wp_footer_php' );

		update_option( 'openhook_wordpress', $wordpress );

		$thesis[ 'thesis_hook_before_html' ][ 'action' ] = stripslashes( get_option( 'openhook_before_html' ) );
		$thesis[ 'thesis_hook_before_html' ][ 'php' ] = get_option( 'openhook_before_html_php' );
		$thesis[ 'thesis_hook_after_html' ][ 'action' ] = stripslashes( get_option( 'openhook_after_html' ) );
		$thesis[ 'thesis_hook_before_html' ][ 'php' ] = get_option( 'openhook_after_html_php' );
		$thesis[ 'thesis_hook_before_header' ][ 'action' ] = stripslashes( get_option( 'openhook_before_header' ) );
		$thesis[ 'thesis_hook_before_header' ][ 'php' ] = get_option( 'openhook_before_header_php' );
		$thesis[ 'thesis_hook_before_header' ][ 'unhook' ][ 'thesis_nav_menu' ] = get_option( 'openhook_before_header_nav_menu' );
		$thesis[ 'thesis_hook_after_header' ][ 'action' ] = stripslashes( get_option( 'openhook_after_header' ) );
		$thesis[ 'thesis_hook_after_header' ][ 'php' ] = get_option( 'openhook_after_header_php' );
		$thesis[ 'thesis_hook_header' ][ 'action' ] = stripslashes( get_option( 'openhook_header' ) );
		$thesis[ 'thesis_hook_header' ][ 'php' ] = get_option( 'openhook_header_php' );
		$thesis[ 'thesis_hook_header' ][ 'unhook' ][ 'thesis_default_header' ] = get_option( 'openhook_header_default_header' );
		$thesis[ 'thesis_hook_before_title' ][ 'action' ] = stripslashes( get_option( 'openhook_before_title' ) );
		$thesis[ 'thesis_hook_before_title' ][ 'php' ] = get_option( 'openhook_before_title_php' );
		$thesis[ 'thesis_hook_after_title' ][ 'action' ] = stripslashes( get_option( 'openhook_after_title' ) );
		$thesis[ 'thesis_hook_after_title' ][ 'php' ] = get_option( 'openhook_after_title_php' );
		$thesis[ 'thesis_hook_before_content_box' ][ 'action' ] = stripslashes( get_option( 'openhook_before_content_box' ) );
		$thesis[ 'thesis_hook_before_content_box' ][ 'php' ] = get_option( 'openhook_before_content_box_php' );
		$thesis[ 'thesis_hook_after_content_box' ][ 'action' ] = stripslashes( get_option( 'openhook_after_content_box' ) );
		$thesis[ 'thesis_hook_after_content_box' ][ 'php' ] = get_option( 'openhook_after_content_box_php' );
		$thesis[ 'thesis_hook_before_content' ][ 'action' ] = stripslashes( get_option( 'openhook_before_content' ) );
		$thesis[ 'thesis_hook_before_content' ][ 'php' ] = get_option( 'openhook_before_content_php' );
		$thesis[ 'thesis_hook_after_content' ][ 'action' ] = stripslashes( get_option( 'openhook_after_content' ) );
		$thesis[ 'thesis_hook_after_content' ][ 'php' ] = get_option( 'openhook_after_content_php' );
		$thesis[ 'thesis_hook_before_content_area' ][ 'action' ] = stripslashes( get_option( 'openhook_before_content_area' ) );
		$thesis[ 'thesis_hook_before_content_area' ][ 'php' ] = get_option( 'openhook_before_content_area_php' );
		$thesis[ 'thesis_hook_after_content_area' ][ 'action' ] = stripslashes( get_option( 'openhook_after_content_area' ) );
		$thesis[ 'thesis_hook_after_content_area' ][ 'php' ] = get_option( 'openhook_after_content_area_php' );
		$thesis[ 'thesis_hook_after_content_area' ][ 'unhook' ][ 'thesis_post_navigation' ] = get_option( 'openhook_after_content_post_navigation' );
		$thesis[ 'thesis_hook_after_content_area' ][ 'unhook' ][ 'thesis_prev_next_posts' ] = get_option( 'openhook_after_content_prev_next_posts' );
		$thesis[ 'thesis_hook_post_box_top' ][ 'action' ] = stripslashes( get_option( 'openhook_post_box_top' ) );
		$thesis[ 'thesis_hook_post_box_top' ][ 'php' ] = get_option( 'openhook_post_box_top_php' );
		$thesis[ 'thesis_hook_post_box_bottom' ][ 'action' ] = stripslashes( get_option( 'openhook_post_box_bottom' ) );
		$thesis[ 'thesis_hook_post_box_bottom' ][ 'php' ] = get_option( 'openhook_post_box_bottom_php' );
		$thesis[ 'thesis_hook_content_box_top' ][ 'action' ] = stripslashes( get_option( 'openhook_content_box_top' ) );
		$thesis[ 'thesis_hook_content_box_top' ][ 'php' ] = get_option( 'openhook_content_box_top_php' );
		$thesis[ 'thesis_hook_content_box_bottom' ][ 'action' ] = stripslashes( get_option( 'openhook_content_box_bottom' ) );
		$thesis[ 'thesis_hook_content_box_bottom' ][ 'php' ] = get_option( 'openhook_content_box_bottom_php' );
		$thesis[ 'thesis_hook_feature_box' ][ 'action' ] = stripslashes( get_option( 'openhook_feature_box' ) );
		$thesis[ 'thesis_hook_feature_box' ][ 'php' ] = get_option( 'openhook_feature_box_php' );
		$thesis[ 'thesis_hook_before_post_box' ][ 'action' ] = stripslashes( get_option( 'openhook_before_post_box' ) );
		$thesis[ 'thesis_hook_before_post_box' ][ 'php' ] = get_option( 'openhook_before_post_box_php' );
		$thesis[ 'thesis_hook_after_post_box' ][ 'action'] = get_option( 'openhook_after_post_box' );
		$thesis[ 'thesis_hook_after_post_box' ][ 'php' ] = get_option( 'openhook_after_post_box_php' );
		$thesis[ 'thesis_hook_before_teasers_box' ][ 'action' ] = stripslashes( get_option( 'openhook_before_teasers_box' ) );
		$thesis[ 'thesis_hook_before_teasers_box' ][ 'php' ] = get_option( 'openhook_before_teasers_box_php' );
		$thesis[ 'thesis_hook_after_teasers_box' ][ 'action' ] = stripslashes( get_option( 'openhook_after_teasers_box' ) );
		$thesis[ 'thesis_hook_after_teasers_box' ][ 'php' ] = get_option( 'openhook_after_teasers_box_php' );
		$thesis[ 'thesis_hook_before_post' ][ 'action' ] = stripslashes( get_option( 'openhook_before_post' ) );
		$thesis[ 'thesis_hook_before_post' ][ 'php'] = get_option( 'openhook_before_post_php' );
		$thesis[ 'thesis_hook_after_post' ][ 'action' ] = stripslashes( get_option( 'openhook_after_post' ) );
		$thesis[ 'thesis_hook_after_post' ][ 'php' ] = get_option( 'openhook_after_post_php' );
		$thesis[ 'thesis_hook_after_post' ][ 'unhook' ][ 'thesis_post_tags' ] = get_option( 'openhook_after_post_post_tags' );
		$thesis[ 'thesis_hook_after_post' ][ 'unhook' ][ 'thesis_comments_link' ] = get_option( 'openhook_after_post_comments_link' );
		$thesis[ 'thesis_hook_before_teaser_box' ][ 'action' ] = stripslashes( get_option( 'openhook_before_teaser_box' ) );
		$thesis[ 'thesis_hook_before_teaser_box' ][ 'php' ] = get_option( 'openhook_before_teaser_box_php' );
		$thesis[ 'thesis_hook_after_teaser_box' ][ 'action' ] = stripslashes( get_option( 'openhook_after_teaser_box' ) );
		$thesis[ 'thesis_hook_after_teaser_box' ][ 'php' ] = get_option( 'openhook_after_teaser_box_php' );
		$thesis[ 'thesis_hook_before_teaser' ][ 'action' ] = stripslashes( get_option( 'openhook_before_teaser' ) );
		$thesis[ 'thesis_hook_before_teaser' ][ 'php' ] = get_option( 'openhook_before_teaser_php' );
		$thesis[ 'thesis_hook_after_teaser' ][ 'action' ] = stripslashes( get_option( 'openhook_after_teaser' ) );
		$thesis[ 'thesis_hook_after_teaser' ][ 'php' ] = get_option( 'openhook_after_teaser_php' );
		$thesis[ 'thesis_hook_before_headline' ][ 'action' ] = stripslashes( get_option( 'openhook_before_headline' ) );
		$thesis[ 'thesis_hook_before_headline' ][ 'php' ] = get_option( 'openhook_before_headline_php' );
		$thesis[ 'thesis_hook_after_headline' ][ 'action' ] = stripslashes( get_option( 'openhook_after_headline' ) );
		$thesis[ 'thesis_hook_after_headline' ][ 'php' ] = get_option( 'openhook_after_headline_php' );
		$thesis[ 'thesis_hook_before_teaser_headline' ][ 'action' ] = stripslashes( get_option( 'openhook_before_teaser_headline' ) );
		$thesis[ 'thesis_hook_before_teaser_headline' ][ 'php' ] = get_option( 'openhook_before_teaser_headline_php' );
		$thesis[ 'thesis_hook_after_teaser_headline' ][ 'action' ] = stripslashes( get_option( 'openhook_after_teaser_headline' ) );
		$thesis[ 'thesis_hook_after_teaser_headline' ][ 'php' ] = get_option( 'openhook_after_teaser_headline_php' );
		$thesis[ 'thesis_hook_byline_item' ][ 'action' ] = stripslashes( get_option( 'openhook_byline_item' ) );
		$thesis[ 'thesis_hook_byline_item' ][ 'php' ] = get_option( 'openhook_byline_item_php' );
		$thesis[ 'thesis_hook_before_comment_meta' ][ 'action' ] = stripslashes( get_option( 'openhook_before_comment_meta' ) );
		$thesis[ 'thesis_hook_before_comment_meta' ][ 'php' ] = get_option( 'openhook_before_comment_meta_php' );
		$thesis[ 'thesis_hook_after_comment_meta' ][ 'action' ] = stripslashes( get_option( 'openhook_after_comment_meta' ) );
		$thesis[ 'thesis_hook_after_comment_meta' ][ 'php' ] = get_option( 'openhook_after_comment_meta_php' );
		$thesis[ 'thesis_hook_comment_field' ][ 'action' ] = stripslashes( get_option( 'openhook_comment_field' ) );
		$thesis[ 'thesis_hook_comment_field' ][ 'php' ] = get_option( 'openhook_comment_field_php' );
		$thesis[ 'thesis_hook_comment_form' ][ 'action' ] = stripslashes( get_option( 'openhook_comment_form' ) );
		$thesis[ 'thesis_hook_comment_form' ][ 'php' ] = get_option( 'openhook_comment_form_php' );
		$thesis[ 'thesis_hook_comment_form' ][ 'unhook' ][ 'show_subscription_checkbox' ] = get_option( 'openhook_comment_form_show_subscription_checkbox' );
		$thesis[ 'thesis_hook_archives_template' ][ 'action' ] = stripslashes( get_option( 'openhook_archives_template' ) );
		$thesis[ 'thesis_hook_archives_template' ][ 'php' ] = get_option( 'openhook_archives_template_php' );
		$thesis[ 'thesis_hook_archives_template' ][ 'unhook' ][ 'thesis_archives_template' ] = get_option( 'openhook_archives_template_archives_template' );
		$thesis[ 'thesis_hook_custom_template' ][ 'action' ] = stripslashes( get_option( 'openhook_custom_template' ) );
		$thesis[ 'thesis_hook_custom_template' ][ 'php' ] = get_option( 'openhook_custom_template_php' );
		$thesis[ 'thesis_hook_custom_template' ][ 'unhook' ][ 'thesis_custom_template_sample' ] = get_option( 'openhook_custom_template_custom_template_sample' );
		$thesis[ 'thesis_hook_faux_admin' ][ 'action' ] = stripslashes( get_option( 'openhook_faux_admin' ) );
		$thesis[ 'thesis_hook_faux_admin' ][ 'php' ] = get_option( 'openhook_faux_admin_php' );
		$thesis[ 'thesis_hook_404_title' ][ 'action' ] = stripslashes( get_option( 'openhook_404_title' ) );
		$thesis[ 'thesis_hook_404_title' ][ 'php' ] = get_option( 'openhook_404_title_php' );
		$thesis[ 'thesis_hook_404_title' ][ 'unhook' ][ 'thesis_404_title' ] = get_option( 'openhook_404_title_404_title' );
		$thesis[ 'thesis_hook_404_content' ][ 'action' ] = stripslashes( get_option( 'openhook_404_content' ) );
		$thesis[ 'thesis_hook_404_content' ][ 'php' ] = get_option( 'openhook_404_content_php' );
		$thesis[ 'thesis_hook_404_content' ][ 'unhook' ][ 'thesis_404_content' ] = get_option( 'openhook_404_content_404_content' );
		$thesis[ 'thesis_hook_before_sidebars' ][ 'action' ] = stripslashes( get_option( 'openhook_before_sidebars' ) );
		$thesis[ 'thesis_hook_before_sidebars' ][ 'php' ] = get_option( 'openhook_before_sidebars_php' );
		$thesis[ 'thesis_hook_after_sidebars' ][ 'action' ] = stripslashes( get_option( 'openhook_after_sidebars' ) );
		$thesis[ 'thesis_hook_after_sidebars' ][ 'php' ] = get_option( 'openhook_after_sidebars_php' );
		$thesis[ 'thesis_hook_after_multimedia_box' ][ 'action' ] = stripslashes( get_option( 'openhook_after_multimedia_box' ) );
		$thesis[ 'thesis_hook_after_multimedia_box' ][ 'php' ] = get_option( 'openhook_after_multimedia_box_php' );
		$thesis[ 'thesis_hook_multimedia_box' ][ 'action' ] = stripslashes( get_option( 'openhook_multimedia_box' ) );
		$thesis[ 'thesis_hook_multimedia_box' ][ 'php' ] = get_option( 'openhook_multimedia_box_php' );
		$thesis[ 'thesis_hook_before_sidebar_1' ][ 'action' ] = stripslashes( get_option( 'openhook_before_sidebar_1' ) );
		$thesis[ 'thesis_hook_before_sidebar_1' ][ 'php' ] = get_option( 'openhook_before_sidebar_1_php' );
		$thesis[ 'thesis_hook_after_sidebar_1' ][ 'action' ] = stripslashes( get_option( 'openhook_after_sidebar_1' ) );
		$thesis[ 'thesis_hook_after_sidebar_1' ][ 'php' ] = get_option( 'openhook_after_sidebar_1_php' );
		$thesis[ 'thesis_hook_before_sidebar_2' ][ 'action' ] = stripslashes( get_option( 'openhook_before_sidebar_2' ) );
		$thesis[ 'thesis_hook_before_sidebar_2' ][ 'php' ] = get_option( 'openhook_before_sidebar_2_php' );
		$thesis[ 'thesis_hook_after_sidebar_2' ][ 'action' ] = stripslashes( get_option( 'openhook_after_sidebar_2' ) );
		$thesis[ 'thesis_hook_after_sidebar_2' ][ 'php' ] = get_option( 'openhook_after_sidebar_2_php' );
		$thesis[ 'thesis_hook_before_footer' ][ 'action' ] = stripslashes( get_option( 'openhook_before_footer' ) );
		$thesis[ 'thesis_hook_before_footer' ][ 'php' ] = get_option( 'openhook_before_footer_php' );
		$thesis[ 'thesis_hook_after_footer' ][ 'action' ] = stripslashes( get_option( 'openhook_after_footer' ) );
		$thesis[ 'thesis_hook_after_footer' ][ 'php' ] = get_option( 'openhook_after_footer_php' );
		$thesis[ 'thesis_hook_footer' ][ 'action' ] = stripslashes( get_option( 'openhook_footer' ) );
		$thesis[ 'thesis_hook_footer' ][ 'php' ] = get_option( 'openhook_footer_php' );
		$thesis[ 'thesis_hook_footer' ][ 'unhook' ][ 'thesis_attribution' ] = get_option( 'openhook_footer_thesis_attribution' );

		update_option( 'openhook_thesis', $thesis );

		echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
<p><strong>' . __( 'You have successfully upgraded to OpenHook 3.', 'openhook' ) . '</strong></p></div>';
	}
	else {
		wp_die( __( 'You do not have permission to do this.', 'openhook' ) );
	}
}

/**
 * Delete legacy options
 */
function openhook_delete_options( $legacy = false ) {
	if ( current_user_can( 'delete_users' ) && defined( 'OPENHOOK_SAFEGUARD' ) ) {
		if ( $legacy == 'legacy' ) {
			delete_option( 'openhook_save_button' );
			delete_option( 'openhook_wp_head' );
			delete_option( 'openhook_wp_head_php' );
			delete_option( 'openhook_before_html' );
			delete_option( 'openhook_before_html_php' );
			delete_option( 'openhook_after_html' );
			delete_option( 'openhook_after_html_php' );
			delete_option( 'openhook_after_html_footer_scripts' );
			delete_option( 'openhook_after_html_ie_clear' );
			delete_option( 'openhook_before_header' );
			delete_option( 'openhook_before_header_php' );
			delete_option( 'openhook_before_header_nav_menu' );
			delete_option( 'openhook_after_header' );
			delete_option( 'openhook_after_header_php' );
			delete_option( 'openhook_header' );
			delete_option( 'openhook_header_php' );
			delete_option( 'openhook_header_default_header' );
			delete_option( 'openhook_before_title' );
			delete_option( 'openhook_before_title_php' );
			delete_option( 'openhook_after_title' );
			delete_option( 'openhook_after_title_php' );
			delete_option( 'openhook_before_content_box' );
			delete_option( 'openhook_before_content_box_php' );
			delete_option( 'openhook_after_content_box' );
			delete_option( 'openhook_after_content_box_php' );
			delete_option( 'openhook_before_content' );
			delete_option( 'openhook_before_content_php' );
			delete_option( 'openhook_after_content' );
			delete_option( 'openhook_after_content_php' );
			delete_option( 'openhook_before_content_area' );
			delete_option( 'openhook_before_content_area_php' );
			delete_option( 'openhook_after_content_area' );
			delete_option( 'openhook_after_content_area_php' );
			delete_option( 'openhook_after_content_post_navigation' );
			delete_option( 'openhook_after_content_prev_next_posts' );
			delete_option( 'openhook_post_box_top' );
			delete_option( 'openhook_post_box_top_php' );
			delete_option( 'openhook_post_box_bottom' );
			delete_option( 'openhook_post_box_bottom_php' );
			delete_option( 'openhook_content_box_top' );
			delete_option( 'openhook_content_box_top_php' );
			delete_option( 'openhook_content_box_bottom' );
			delete_option( 'openhook_content_box_bottom_php' );
			delete_option( 'openhook_feature_box' );
			delete_option( 'openhook_feature_box_php' );
			delete_option( 'openhook_before_post_box' );
			delete_option( 'openhook_before_post_box_php' );
			delete_option( 'openhook_before_post_box_add_post_image' );
			delete_option( 'openhook_after_post_box' );
			delete_option( 'openhook_after_post_box_php' );
			delete_option( 'openhook_before_teasers_box' );
			delete_option( 'openhook_before_teasers_box_php' );
			delete_option( 'openhook_after_teasers_box' );
			delete_option( 'openhook_after_teasers_box_php' );
			delete_option( 'openhook_before_post' );
			delete_option( 'openhook_before_post_php' );
			delete_option( 'openhook_after_post' );
			delete_option( 'openhook_after_post_php' );
			delete_option( 'openhook_after_post_trackback_rdf' );
			delete_option( 'openhook_after_post_post_tags' );
			delete_option( 'openhook_after_post_comments_link' );
			delete_option( 'openhook_before_teaser_box' );
			delete_option( 'openhook_before_teaser_box_php' );
			delete_option( 'openhook_before_teaser_box_add_thumb' );
			delete_option( 'openhook_after_teaser_box' );
			delete_option( 'openhook_after_teaser_box_php' );
			delete_option( 'openhook_before_teaser' );
			delete_option( 'openhook_before_teaser_php' );
			delete_option( 'openhook_after_teaser' );
			delete_option( 'openhook_after_teaser_php' );
			delete_option( 'openhook_before_headline' );
			delete_option( 'openhook_before_headline_php' );
			delete_option( 'openhook_after_headline' );
			delete_option( 'openhook_after_headline_php' );
			delete_option( 'openhook_before_teaser_headline' );
			delete_option( 'openhook_before_teaser_headline_php' );
			delete_option( 'openhook_after_teaser_headline' );
			delete_option( 'openhook_after_teaser_headline_php' );
			delete_option( 'openhook_byline_item' );
			delete_option( 'openhook_byline_item_php' );
			delete_option( 'openhook_before_comment_meta' );
			delete_option( 'openhook_before_comment_meta_php' );
			delete_option( 'openhook_after_comment_meta' );
			delete_option( 'openhook_after_comment_meta_php' );
			delete_option( 'openhook_comment_field' );
			delete_option( 'openhook_comment_field_php' );
			delete_option( 'openhook_comment_form' );
			delete_option( 'openhook_comment_form_php' );
			delete_option( 'openhook_comment_form_show_subscription_checkbox' );
			delete_option( 'openhook_archives_template' );
			delete_option( 'openhook_archives_template_php' );
			delete_option( 'openhook_archives_template_archives_template' );
			delete_option( 'openhook_archive_info_default_archive_info' );
			delete_option( 'openhook_custom_template' );
			delete_option( 'openhook_custom_template_php' );
			delete_option( 'openhook_custom_template_custom_template_sample' );
			delete_option( 'openhook_faux_admin' );
			delete_option( 'openhook_faux_admin_php' );
			delete_option( 'openhook_archive_info' );
			delete_option( 'openhook_archive_info_php' );
			delete_option( 'openhook_404_title' );
			delete_option( 'openhook_404_title_php' );
			delete_option( 'openhook_404_title_404_title' );
			delete_option( 'openhook_404_content' );
			delete_option( 'openhook_404_content_php' );
			delete_option( 'openhook_404_content_404_content' );
			delete_option( 'openhook_before_sidebars' );
			delete_option( 'openhook_before_sidebars_php' );
			delete_option( 'openhook_after_sidebars' );
			delete_option( 'openhook_after_sidebars_php' );
			delete_option( 'openhook_after_multimedia_box' );
			delete_option( 'openhook_after_multimedia_box_php' );
			delete_option( 'openhook_multimedia_box' );
			delete_option( 'openhook_multimedia_box_php' );
			delete_option( 'openhook_before_sidebar_1' );
			delete_option( 'openhook_before_sidebar_1_php' );
			delete_option( 'openhook_after_sidebar_1' );
			delete_option( 'openhook_after_sidebar_1_php' );
			delete_option( 'openhook_before_sidebar_2' );
			delete_option( 'openhook_before_sidebar_2_php' );
			delete_option( 'openhook_after_sidebar_2' );
			delete_option( 'openhook_after_sidebar_2_php' );
			delete_option( 'openhook_before_footer' );
			delete_option( 'openhook_before_footer_php' );
			delete_option( 'openhook_after_footer' );
			delete_option( 'openhook_after_footer_php' );
			delete_option( 'openhook_footer' );
			delete_option( 'openhook_footer_php' );
			delete_option( 'openhook_footer_thesis_attribution' );
			delete_option( 'openhook_footer_debug_info' );
			delete_option( 'openhook_footer_honeypot' );
			delete_option( 'openhook_wp_footer' );
			delete_option( 'openhook_wp_footer_php' );
			delete_option( 'openhook_footer_admin_link' );
	
			echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
	<p><strong>' . __( 'Old options deleted.', 'openhook' ) . '</strong></p></div>';
		}
		else {
			delete_option( 'openhook_general' );
			delete_option( 'openhook_thesis' );
			delete_option( 'openhook_wordpress' );

			echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
	<p><strong>' . __( 'OpenHook has been uninstalled.', 'openhook' ) . '</strong></p></div>';
		}
	}
	else {
		wp_die( __( 'You do not have permission to do this.', 'openhook' ) );
	}
}