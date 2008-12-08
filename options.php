<?php
/*
Options page for the Thesis OpenHook Plugin
http://rickbeckman.com/thesis-openhook/

Copyright 2008  Eric Marden  (email : wp@xentek.net)

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
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Assign plugin path.
$path = PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));

// If this is a POST, we need to update the options to the newly submitted values.
if (!empty($_POST)) {

	if (isset($_POST['openhook_custom_css']) && current_user_can('level_10')) {
		$contents = stripslashes($_POST['openhook_custom_css']);
		$custom_css = ABSPATH . '/wp-content/themes/thesis/custom/custom.css';
		$custom_css = @fopen($custom_css, 'w') or die ('Error editing.');
		@fwrite($custom_css, $contents);
		@fclose($custom_css) or die ('Error closing file.');
	}

	update_option('openhook_before_html',						$_POST['openhook_before_html']);
	if (!isset($_POST['openhook_before_html_php']))				$_POST['openhook_before_html_php']			= 0;
	update_option('openhook_before_html_php',					$_POST['openhook_before_html_php']);

	update_option('openhook_after_html',						$_POST['openhook_after_html']);
	if (!isset($_POST['openhook_after_html_php']))				$_POST['openhook_after_html_php']			= 0;
	update_option('openhook_after_html_php',					$_POST['openhook_after_html_php']);
	if (!isset($_POST['openhook_after_html_footer_scripts']))	$_POST['openhook_after_html_footer_scripts']= 0;
	update_option('openhook_after_html_footer_scripts',			$_POST['openhook_after_html_footer_scripts']);
	if (!isset($_POST['openhook_after_html_ie_clear']))			$_POST['openhook_after_html_ie_clear']		= 0;
	update_option('openhook_after_html_ie_clear',				$_POST['openhook_after_html_ie_clear']);

	update_option('openhook_before_header',						$_POST['openhook_before_header']);
	if (!isset($_POST['openhook_before_header_php']))			$_POST['openhook_before_header_php']		= 0;
	update_option('openhook_before_header_php',					$_POST['openhook_before_header_php']);
	if (!isset($_POST['openhook_before_header_nav_menu']))		$_POST['openhook_before_header_nav_menu']	= 0;
	update_option('openhook_before_header_nav_menu',			$_POST['openhook_before_header_nav_menu']);

	update_option('openhook_after_header',						$_POST['openhook_after_header']);
	if (!isset($_POST['openhook_after_header_php']))			$_POST['openhook_after_header_php']			= 0;
	update_option('openhook_after_header_php',					$_POST['openhook_after_header_php']);

	update_option('openhook_header',							$_POST['openhook_header']);
	if (!isset($_POST['openhook_header_php']))					$_POST['openhook_header_php']				= 0;
	update_option('openhook_header_php',						$_POST['openhook_header_php']);
	if (!isset($_POST['openhook_header_default_header']))		$_POST['openhook_header_default_header']	= 0;
	update_option('openhook_header_default_header',				$_POST['openhook_header_default_header']);

	update_option('openhook_before_title',						$_POST['openhook_before_title']);
	if (!isset($_POST['openhook_before_title_php']))			$_POST['openhook_before_title_php']			= 0;
	update_option('openhook_before_title_php',					$_POST['openhook_before_title_php']);

	update_option('openhook_after_title',						$_POST['openhook_after_title']);
	if (!isset($_POST['openhook_after_title_php']))				$_POST['openhook_after_title_php']			= 0;
	update_option('openhook_after_title_php',					$_POST['openhook_after_title_php']);

	update_option('openhook_before_content',					$_POST['openhook_before_content']);
	if (!isset($_POST['openhook_before_content_php']))			$_POST['openhook_before_content_php']		= 0;
	update_option('openhook_before_content_php',				$_POST['openhook_before_content_php']);

	update_option('openhook_after_content',						$_POST['openhook_after_content']);
	if (!isset($_POST['openhook_after_content_php']))			$_POST['openhook_after_content_php']		= 0;
	update_option('openhook_after_content_php',					$_POST['openhook_after_content_php']);
	if (!isset($_POST['openhook_after_content_post_navigation'])) $_POST['openhook_after_content_post_navigation'] = 0;
	update_option('openhook_after_content_post_navigation',		$_POST['openhook_after_content_post_navigation']);
	if (!isset($_POST['openhook_after_content_prev_next_posts'])) $_POST['openhook_after_content_prev_next_posts'] = 0;
	update_option('openhook_after_content_prev_next_posts',		$_POST['openhook_after_content_prev_next_posts']);

	update_option('openhook_before_post',						$_POST['openhook_before_post']);
	if (!isset($_POST['openhook_before_post_php']))				$_POST['openhook_before_post_php']			= 0;
	update_option('openhook_before_post_php',					$_POST['openhook_before_post_php']);

	update_option('openhook_after_post',						$_POST['openhook_after_post']);
	if (!isset($_POST['openhook_after_post_php']))				$_POST['openhook_after_post_php']			= 0;
	update_option('openhook_after_post_php',					$_POST['openhook_after_post_php']);
	if (!isset($_POST['openhook_after_post_trackback_rdf']))	$_POST['openhook_after_post_trackback_rdf']	= 0;
	update_option('openhook_after_post_trackback_rdf',			$_POST['openhook_after_post_trackback_rdf']);
	if (!isset($_POST['openhook_after_post_post_tags']))		$_POST['openhook_after_post_post_tags']		= 0;
	update_option('openhook_after_post_post_tags',				$_POST['openhook_after_post_post_tags']);
	if (!isset($_POST['openhook_after_post_comments_link']))	$_POST['openhook_after_post_comments_link']	= 0;
	update_option('openhook_after_post_comments_link',			$_POST['openhook_after_post_comments_link']);

	update_option('openhook_before_headline',					$_POST['openhook_before_headline']);
	if (!isset($_POST['openhook_before_headline_php']))			$_POST['openhook_before_headline_php']		= 0;
	update_option('openhook_before_headline_php',				$_POST['openhook_before_headline_php']);

	update_option('openhook_after_headline',					$_POST['openhook_after_headline']);
	if (!isset($_POST['openhook_after_headline_php']))			$_POST['openhook_after_headline_php']		= 0;
	update_option('openhook_after_headline_php',				$_POST['openhook_after_headline_php']);

	update_option('openhook_byline_item',						$_POST['openhook_byline_item']);
	if (!isset($_POST['openhook_byline_item_php']))				$_POST['openhook_byline_item_php']			= 0;
	update_option('openhook_byline_item_php',					$_POST['openhook_byline_item_php']);

	update_option('openhook_before_comment_meta',				$_POST['openhook_before_comment_meta']);
	if (!isset($_POST['openhook_before_comment_meta_php']))		$_POST['openhook_before_comment_meta_php']	= 0;
	update_option('openhook_before_comment_meta_php',			$_POST['openhook_before_comment_meta_php']);

	update_option('openhook_after_comment_meta',				$_POST['openhook_after_comment_meta']);
	if (!isset($_POST['openhook_after_comment_meta_php']))		$_POST['openhook_after_comment_meta_php']	= 0;
	update_option('openhook_after_comment_meta_php',			$_POST['openhook_after_comment_meta_php']);

	update_option('openhook_after_comment',						$_POST['openhook_after_comment']);
	if (!isset($_POST['openhook_after_comment_php']))			$_POST['openhook_after_comment_php']		= 0;
	update_option('openhook_after_comment_php',					$_POST['openhook_after_comment_php']);
	if (!isset($_POST['openhook_comment_form_show_subscription_checkbox'])) $_POST['openhook_comment_form_show_subscription_checkbox'] = 0;
	update_option('openhook_comment_form_show_subscription_checkbox', $_POST['openhook_comment_form_show_subscription_checkbox']);

	update_option('openhook_comment_form',						$_POST['openhook_comment_form']);
	if (!isset($_POST['openhook_comment_form_php']))			$_POST['openhook_comment_form_php']			= 0;
	update_option('openhook_comment_form_php',					$_POST['openhook_comment_form_php']);

	update_option('openhook_archives_template',					$_POST['openhook_archives_template']);
	if (!isset($_POST['openhook_archives_template_php']))		$_POST['openhook_archives_template_php']	= 0;
	update_option('openhook_archives_template_php',				$_POST['openhook_archives_template_php']);
	if (!isset($_POST['openhook_archives_template_archives_template'])) $_POST['openhook_archives_template_archives_template'] = 0;
	update_option('openhook_archives_template_archives_template', $_POST['openhook_archives_template_archives_template']);

	update_option('openhook_custom_template',					$_POST['openhook_custom_template']);
	if (!isset($_POST['openhook_custom_template_php']))			$_POST['openhook_custom_template_php']		= 0;
	update_option('openhook_custom_template_php',				$_POST['openhook_custom_template_php']);
	if (!isset($_POST['openhook_custom_template_custom_template_sample'])) $_POST['openhook_custom_template_custom_template_sample'] = 0;
	update_option('openhook_custom_template_custom_template_sample', $_POST['openhook_custom_template_custom_template_sample']);

	update_option('openhook_archive_info',						$_POST['openhook_archive_info']);
	if (!isset($_POST['openhook_archive_info_php']))			$_POST['openhook_archive_info_php']			= 0;
	update_option('openhook_archive_info_php',					$_POST['openhook_archive_info_php']);
	if (!isset($_POST['openhook_archive_info_default_archive_info'])) $_POST['openhook_archive_info_default_archive_info'] = 0;
	update_option('openhook_archive_info_default_archive_info',	$_POST['openhook_archive_info_default_archive_info']);

	update_option('openhook_404_title',							$_POST['openhook_404_title']);
	if (!isset($_POST['openhook_404_title_php']))				$_POST['openhook_404_title_php']			= 0;
	update_option('openhook_404_title_php',						$_POST['openhook_404_title_php']);
	if (!isset($_POST['openhook_404_title_404_title']))			$_POST['openhook_404_title_404_title']		= 0;
	update_option('openhook_404_title_404_title',				$_POST['openhook_404_title_404_title']);

	update_option('openhook_404_content',						$_POST['openhook_404_content']);
	if (!isset($_POST['openhook_404_content_php']))				$_POST['openhook_404_content_php']			= 0;
	update_option('openhook_404_content_php',					$_POST['openhook_404_content_php']);
	if (!isset($_POST['openhook_404_content_404_content']))		$_POST['openhook_404_content_404_content']	= 0;
	update_option('openhook_404_content_404_content',			$_POST['openhook_404_content_404_content']);

	update_option('openhook_before_sidebars',					$_POST['openhook_before_sidebars']);
	if (!isset($_POST['openhook_before_sidebars_php']))			$_POST['openhook_before_sidebars_php']		= 0;
	update_option('openhook_before_sidebars_php',				$_POST['openhook_before_sidebars_php']);

	update_option('openhook_after_sidebars',					$_POST['openhook_after_sidebars']);
	if (!isset($_POST['openhook_after_sidebars_php']))			$_POST['openhook_after_sidebars_php']		= 0;
	update_option('openhook_after_sidebars_php',				$_POST['openhook_after_sidebars_php']);

	update_option('openhook_multimedia_box',					$_POST['openhook_multimedia_box']);
	if (!isset($_POST['openhook_multimedia_box_php']))			$_POST['openhook_multimedia_box_php']		= 0;
	update_option('openhook_multimedia_box_php',				$_POST['openhook_multimedia_box_php']);

	update_option('openhook_after_multimedia_box',				$_POST['openhook_after_multimedia_box']);
	if (!isset($_POST['openhook_after_multimedia_box_php']))	$_POST['openhook_after_multimedia_box_php']	= 0;
	update_option('openhook_after_multimedia_box_php',			$_POST['openhook_after_multimedia_box_php']);

	update_option('openhook_before_sidebar_1',					$_POST['openhook_before_sidebar_1']);
	if (!isset($_POST['openhook_before_sidebar_1_php']))		$_POST['openhook_before_sidebar_1_php']		= 0;
	update_option('openhook_before_sidebar_1_php',				$_POST['openhook_before_sidebar_1_php']);

	update_option('openhook_after_sidebar_1',					$_POST['openhook_after_sidebar_1']);
	if (!isset($_POST['openhook_after_sidebar_1_php']))			$_POST['openhook_after_sidebar_1_php']		= 0;
	update_option('openhook_after_sidebar_1_php',				$_POST['openhook_after_sidebar_1_php']);

	update_option('openhook_before_sidebar_2',					$_POST['openhook_before_sidebar_2']);
	if (!isset($_POST['openhook_before_sidebar_2_php']))		$_POST['openhook_before_sidebar_2_php']		= 0;
	update_option('openhook_before_sidebar_2_php',				$_POST['openhook_before_sidebar_2_php']);

	update_option('openhook_after_sidebar_2',					$_POST['openhook_after_sidebar_2']);
	if (!isset($_POST['openhook_after_sidebar_2_php']))			$_POST['openhook_after_sidebar_2_php']		= 0;
	update_option('openhook_after_sidebar_2_php',				$_POST['openhook_after_sidebar_2_php']);

	update_option('openhook_before_footer',						$_POST['openhook_before_footer']);
	if (!isset($_POST['openhook_before_footer_php']))			$_POST['openhook_before_footer_php']		= 0;
	update_option('openhook_before_footer_php',					$_POST['openhook_before_footer_php']);

	update_option('openhook_after_footer',						$_POST['openhook_after_footer']);
	if (!isset($_POST['openhook_after_footer_php']))			$_POST['openhook_after_footer_php']			= 0;
	update_option('openhook_after_footer_php',					$_POST['openhook_after_footer_php']);

	update_option('openhook_footer',							$_POST['openhook_footer']);
	if (!isset($_POST['openhook_footer_php']))					$_POST['openhook_footer_php']				= 0;
	update_option('openhook_footer_php',						$_POST['openhook_footer_php']);
	if (!isset($_POST['openhook_footer_thesis_attribution']))	$_POST['openhook_footer_thesis_attribution']= 0;
	update_option('openhook_footer_thesis_attribution',			$_POST['openhook_footer_thesis_attribution']);
	if (!isset($_POST['openhook_footer_admin_link']))			$_POST['openhook_footer_admin_link']		= 0;
	update_option('openhook_footer_admin_link',					$_POST['openhook_footer_admin_link']);
	if (!isset($_POST['openhook_footer_debug_info']))			$_POST['openhook_footer_debug_info']		= 0;
	update_option('openhook_footer_debug_info',					$_POST['openhook_footer_debug_info']);

?>
<div id="message" class="updated fade"><p><strong><em><?php _e('Options Saved!', 'thesis_openhook'); ?></em> <?php _e('Oh, snap! Looks like we have customizations... Better go check your blog to make sure everything looks sexy!', 'thesis_openhook'); ?></strong></p></div>
<?php

}

// Get custom.css contents
if (current_user_can('level_10') && is_writable(ABSPATH . '/wp-content/themes/thesis/custom/custom.css')) {
	$filename = ABSPATH . '/wp-content/themes/thesis/custom/custom.css';
	$handle = @fopen($filename, 'r');
	$contents = @fread($handle, filesize($filename));
	@fclose($handle);
}

// Pull option values for checkbox pre-ticking.
$openhook_before_html_php				= get_option('openhook_before_html_php');
$openhook_after_html_php				= get_option('openhook_after_html_php');
$openhook_after_html_footer_scripts		= get_option('openhook_after_html_footer_scripts');
$openhook_after_html_ie_clear			= get_option('openhook_after_html_ie_clear');
$openhook_before_header_php				= get_option('openhook_before_header_php');
$openhook_before_header_nav_menu		= get_option('openhook_before_header_nav_menu');
$openhook_after_header_php				= get_option('openhook_after_header_php');
$openhook_header_php					= get_option('openhook_header_php');
$openhook_header_default_header			= get_option('openhook_header_default_header');
$openhook_before_title_php				= get_option('openhook_before_title_php');
$openhook_after_title_php				= get_option('openhook_after_title_php');
$openhook_before_content_php			= get_option('openhook_before_content_php');
$openhook_after_content_php				= get_option('openhook_after_content_php');
$openhook_after_content_post_navigation	= get_option('openhook_after_content_post_navigation');
$openhook_after_content_prev_next_posts	= get_option('openhook_after_content_prev_next_posts');
$openhook_before_post_php				= get_option('openhook_before_post_php');
$openhook_after_post_php				= get_option('openhook_after_post_php');
$openhook_after_post_trackback_rdf		= get_option('openhook_after_post_trackback_rdf');
$openhook_after_post_post_tags			= get_option('openhook_after_post_post_tags');
$openhook_after_post_comments_link		= get_option('openhook_after_post_comments_link');
$openhook_before_headline_php			= get_option('openhook_before_headline_php');
$openhook_after_headline_php			= get_option('openhook_after_headline_php');
$openhook_byline_item_php				= get_option('openhook_byline_item_php');
$openhook_before_comment_meta_php		= get_option('openhook_before_comment_meta_php');
$openhook_after_comment_meta_php		= get_option('openhook_after_comment_meta_php');
$openhook_after_comment_php				= get_option('openhook_after_comment_php');
$openhook_comment_form_php				= get_option('openhook_comment_form_php');
$openhook_comment_form_show_subscription_checkbox = get_option('openhook_comment_form_show_subscription_checkbox');
$openhook_archives_template_php			= get_option('openhook_archives_template_php');
$openhook_archives_template_archives_template = get_option('openhook_archives_template_archives_template');
$openhook_custom_template_php			= get_option('openhook_custom_template_php');
$openhook_custom_template_custom_template_sample = get_option('openhook_custom_template_custom_template_sample');
$openhook_archive_info_php				= get_option('openhook_archive_info_php');
$openhook_archive_info_default_archive_info = get_option('openhook_archive_info_default_archive_info');
$openhook_404_title_php					= get_option('openhook_404_title_php');
$openhook_404_title_404_title			= get_option('openhook_404_title_404_title');
$openhook_404_content_php				= get_option('openhook_404_content_php');
$openhook_404_content_404_content		= get_option('openhook_404_content_404_content');
$openhook_before_sidebars_php			= get_option('openhook_before_sidebars_php');
$openhook_after_sidebars_php			= get_option('openhook_after_sidebars_php');
$openhook_multimedia_box_php			= get_option('openhook_multimedia_box_php');
$openhook_after_multimedia_box_php		= get_option('openhook_after_multimedia_box_php');
$openhook_before_sidebar_1_php			= get_option('openhook_before_sidebar_1_php');
$openhook_after_sidebar_1_php			= get_option('openhook_after_sidebar_1_php');
$openhook_before_sidebar_2_php			= get_option('openhook_before_sidebar_2_php');
$openhook_after_sidebar_2_php			= get_option('openhook_after_sidebar_2_php');
$openhook_before_footer_php				= get_option('openhook_before_footer_php');
$openhook_after_footer_php				= get_option('openhook_after_footer_php');
$openhook_footer_php					= get_option('openhook_footer_php');
$openhook_footer_thesis_attribution		= get_option('openhook_footer_thesis_attribution');
$openhook_footer_admin_link				= get_option('openhook_footer_admin_link');
$openhook_footer_debug_info				= get_option('openhook_footer_debug_info');
?>
<script type="text/javascript" src="/<?php echo $path; ?>/jquery.textarearesizer.compressed.js"></script>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
	/* jQuery textarea resizer plugin usage */
	jQuery(document).ready(function() {
		jQuery('textarea.resizable:not(.processed)').TextAreaResizer();
	});
//--><!]]>
</script>
<style type="text/css" media="screen">
/* //<![CDATA[ */ <!--
label.head { display: block; font-weight: bold; font-size: 120%; margin-bottom: 5px; width: 720px; padding: 5px; }
label.small { font-weight: bold; font-size: 80%; }
.hook_note { font-size: 80%; }
div.hook { border-bottom: 1px solid #ebebeb; margin-bottom: 10px; padding: 20px; }
p.info { border-top: 1px solid #ebebeb; }
small { font-weight: bold; }
div.grippie { background: #eee url(/<?php echo $path; ?>/grippie.png) no-repeat scroll center center; border-color: #ddd; border-style: solid; border-width: 0 1px 1px; cursor: s-resize; height: 9px; overflow: hidden; }
.resizable-textarea textarea { display: block; margin-bottom: 0; font-family: monospace; height: 50px; }
//--> /* //]]> */
</style>
<div class="wrap">
	<h2><?php _e('Thesis OpenHook', 'thesis_openhook'); ?></h2>
	<p><?php _e('Be prepared to get hooked up! This plugin allows you to insert any content you want into any of the custom hooks within the <a href="http://get-thesis.com/">Thesis theme</a>.', 'thesis_openhook')?> <?php _e('The hook names are pretty self explanatory, but if you need more help determining where they show up in your mark-up,', 'thesis_openhook');?> <a href="http://diythemes.com/thesis/rtfm/hooks/" title="<?php _e('Thesis Hooks Reference', 'thesis_openhook'); ?>"><?php _e('check the manual', 'thesis_openhook'); ?></a>.</p>
	<p><?php _e('Got questions? Just ask me! ', 'thesis_openhook'); ?><a href="http://rickbeckman.com/thesis-openhook/" title="<?php _e('Thesis OpenHook Release Page', 'thesis_openhook'); ?>"><?php _e('Thesis OpenHook Home', 'thesis_openhook'); ?></a>.</p>
	<p class="info"><strong><?php _e('Insert any <abbr title="Hypertext Markup Language" class="initialism">HTML</abbr>, <abbr title="Cascading Style Sheets" class="initialism">CSS</abbr>, JavaScript or <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> you like.', 'thesis_openhook'); ?></strong>
	<br /><small><?php _e('Your <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> code must be enclosed within <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> tags, and you have to enable the “Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook” option for each hook separately.', 'thesis_openhook'); ?></small>
	</p>
	

	<form method="post" action="" id="k2hookup-settings">
		<?php wp_nonce_field('update-options'); ?>
		<input type="hidden" name="action" value="update" />
<?php if ($contents) { ?>
		<input type="hidden" name="page_options" value="openhook_custom_css" />
<?php } ?>
		<input type="hidden" name="page_options" value="openhook_before_html" />
		<input type="hidden" name="page_options" value="openhook_before_html_php" />
		<input type="hidden" name="page_options" value="openhook_after_html" />
		<input type="hidden" name="page_options" value="openhook_after_html_php" />
		<input type="hidden" name="page_options" value="openhook_after_html_footer_scripts" />
		<input type="hidden" name="page_options" value="openhook_after_html_ie_clear" />
		<input type="hidden" name="page_options" value="openhook_before_header" />
		<input type="hidden" name="page_options" value="openhook_before_header_php" />
		<input type="hidden" name="page_options" value="openhook_before_header_nav_menu" />
		<input type="hidden" name="page_options" value="openhook_after_header" />
		<input type="hidden" name="page_options" value="openhook_after_header_php" />
		<input type="hidden" name="page_options" value="openhook_header" />
		<input type="hidden" name="page_options" value="openhook_header_php" />
		<input type="hidden" name="page_options" value="openhook_header_default_header" />
		<input type="hidden" name="page_options" value="openhook_before_title" />
		<input type="hidden" name="page_options" value="openhook_before_title_php" />
		<input type="hidden" name="page_options" value="openhook_after_title" />
		<input type="hidden" name="page_options" value="openhook_after_title_php" />
		<input type="hidden" name="page_options" value="openhook_before_content" />
		<input type="hidden" name="page_options" value="openhook_before_content_php" />
		<input type="hidden" name="page_options" value="openhook_after_content" />
		<input type="hidden" name="page_options" value="openhook_after_content_php" />
		<input type="hidden" name="page_options" value="openhook_after_content_post_navigation" />
		<input type="hidden" name="page_options" value="openhook_after_content_prev_next_posts" />
		<input type="hidden" name="page_options" value="openhook_before_post" />
		<input type="hidden" name="page_options" value="openhook_before_post_php" />
		<input type="hidden" name="page_options" value="openhook_after_post" />
		<input type="hidden" name="page_options" value="openhook_after_post_php" />
		<input type="hidden" name="page_options" value="openhook_after_post_trackback_rdf" />
		<input type="hidden" name="page_options" value="openhook_after_post_post_tags" />
		<input type="hidden" name="page_options" value="openhook_after_post_comments_link" />
		<input type="hidden" name="page_options" value="openhook_before_headline" />
		<input type="hidden" name="page_options" value="openhook_before_headline_php" />
		<input type="hidden" name="page_options" value="openhook_after_headline" />
		<input type="hidden" name="page_options" value="openhook_after_headline_php" />
		<input type="hidden" name="page_options" value="openhook_byline_item" />
		<input type="hidden" name="page_options" value="openhook_byline_item_php" />
		<input type="hidden" name="page_options" value="openhook_before_comment_meta" />
		<input type="hidden" name="page_options" value="openhook_before_comment_meta_php" />
		<input type="hidden" name="page_options" value="openhook_after_comment_meta" />
		<input type="hidden" name="page_options" value="openhook_after_comment_meta_php" />
		<input type="hidden" name="page_options" value="openhook_comment_form" />
		<input type="hidden" name="page_options" value="openhook_comment_form_php" />
		<input type="hidden" name="page_options" value="openhook_comment_form_show_subscription_checkbox" />
		<input type="hidden" name="page_options" value="openhook_archives_template" />
		<input type="hidden" name="page_options" value="openhook_archives_template_php" />
		<input type="hidden" name="page_options" value="openhook_archives_template_archives_template" />
		<input type="hidden" name="page_options" value="openhook_archive_info_default_archive_info" />
		<input type="hidden" name="page_options" value="openhook_custom_template" />
		<input type="hidden" name="page_options" value="openhook_custom_template_php" />
		<input type="hidden" name="page_options" value="openhook_custom_template_custom_template_sample" />
		<input type="hidden" name="page_options" value="openhook_archive_info" />
		<input type="hidden" name="page_options" value="openhook_archive_info_php" />
		<input type="hidden" name="page_options" value="openhook_404_title" />
		<input type="hidden" name="page_options" value="openhook_404_title_php" />
		<input type="hidden" name="page_options" value="openhook_404_title_404_title" />
		<input type="hidden" name="page_options" value="openhook_404_content" />
		<input type="hidden" name="page_options" value="openhook_404_content_php" />
		<input type="hidden" name="page_options" value="openhook_404_content_404_content" />
		<input type="hidden" name="page_options" value="openhook_before_sidebars" />
		<input type="hidden" name="page_options" value="openhook_before_sidebars_php" />
		<input type="hidden" name="page_options" value="openhook_after_sidebars" />
		<input type="hidden" name="page_options" value="openhook_after_sidebars_php" />
		<input type="hidden" name="page_options" value="openhook_after_multimedia_box" />
		<input type="hidden" name="page_options" value="openhook_after_multimedia_box_php" />
		<input type="hidden" name="page_options" value="openhook_multimedia_box" />
		<input type="hidden" name="page_options" value="openhook_multimedia_box_php" />
		<input type="hidden" name="page_options" value="openhook_before_sidebar_1" />
		<input type="hidden" name="page_options" value="openhook_before_sidebar_1_php" />
		<input type="hidden" name="page_options" value="openhook_after_sidebar_1" />
		<input type="hidden" name="page_options" value="openhook_after_sidebar_1_php" />
		<input type="hidden" name="page_options" value="openhook_before_sidebar_2" />
		<input type="hidden" name="page_options" value="openhook_before_sidebar_2_php" />
		<input type="hidden" name="page_options" value="openhook_after_sidebar_2" />
		<input type="hidden" name="page_options" value="openhook_after_sidebar_2_php" />
		<input type="hidden" name="page_options" value="openhook_before_footer" />
		<input type="hidden" name="page_options" value="openhook_before_footer_php" />
		<input type="hidden" name="page_options" value="openhook_after_footer" />
		<input type="hidden" name="page_options" value="openhook_after_footer_php" />
		<input type="hidden" name="page_options" value="openhook_footer" />
		<input type="hidden" name="page_options" value="openhook_footer_php" />
		<input type="hidden" name="page_options" value="openhook_footer_thesis_attribution" />
		<input type="hidden" name="page_options" value="openhook_footer_admin_link" />
		<input type="hidden" name="page_options" value="openhook_footer_debug_info" />

<?php if ($contents) { ?>
		<div class="hook">
			<label class="head" for="openhook_custom_css">custom.css:</label>
			<p><?php _e('You can edit your Thesis <code>custom/custom.css</code> file here! Prior to making big changes, though, be sure to backup the contents of the file for easy reversion!', 'thesis_openhook'); ?></p>
			<textarea class="resizable" id="openhook_custom_css" name="openhook_custom_css" rows="15" cols="88"><?php echo stripslashes($contents); ?></textarea>
		</div>
<?php } ?>

		<div class="hook">
			<label class="head" for="openhook_before_html">Before HTML:</label>
			<textarea class="resizable" id="openhook_before_html" name="openhook_before_html" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_before_html')); ?></textarea>
			<br />
			<input <?php if ($openhook_before_html_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_before_html_php" id="openhook_before_html_php" /> <label for="openhook_before_html_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_html">After HTML:</label>
			<textarea class="resizable" id="openhook_after_html" name="openhook_after_html" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_html')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_html_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_html_php" id="openhook_after_html_php" /> <label for="openhook_after_html_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
			<br />
			<input <?php if ($openhook_after_html_footer_scripts) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_html_footer_scripts" id="openhook_after_html_footer_scripts" /> <label for="openhook_after_html_footer_scripts" class="small"><?php _e('Remove Thesis footer scripts', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('Thesis footer scripts are specified in the Thesis options panel; if you wish to disable them without clearing the data in the Thesis Options, you may do so by removing the action itself.', 'thesis_openhook'); ?></span>
			<br />
			<input <?php if ($openhook_after_html_ie_clear) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_html_ie_clear" id="openhook_after_html_ie_clear" /> <label for="openhook_after_html_ie_clear" class="small"><?php _e('Remove Thesis <abbr title="Internet Explorer" class="initialism">IE</abbr> clear', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('Don’t care about Internet Explorer? Then don’t output this bit of compatibility code! (Not recommended.)', 'thesis_openhook'); ?></span>
		</div>

		<div class="hook">
			<label class="head" for="openhook_before_header">Before Header:</label>
			<textarea class="resizable" id="openhook_before_header" name="openhook_before_header" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_before_header')); ?></textarea>
			<br />
			<input <?php if ($openhook_before_header_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_before_header_php" id="openhook_before_header_php" /> <label for="openhook_before_header_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
			<br />
			<input <?php if ($openhook_before_header_nav_menu) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_before_header_nav_menu" id="openhook_before_header_nav_menu" /> <label for="openhook_before_header_nav_menu" class="small"><?php _e('Remove Thesis nav menu', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('To move your navigation menu to below your header, remove it here, then include <code>&lt;?php thesis_nav_menu(); ?&gt;</code> in the “After Header” hook.', 'thesis_openhook'); ?></span>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_header">After Header:</label>
			<textarea class="resizable" id="openhook_after_header" name="openhook_after_header" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_header')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_header_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_header_php" id="openhook_after_header_php" /> <label for="openhook_after_header_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_header">Header:</label>
			<textarea class="resizable" id="openhook_header" name="openhook_header" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_header')); ?></textarea>
			<br />
			<input <?php if ($openhook_header_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_header_php" id="openhook_header_php" /> <label for="openhook_header_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
			<br />
			<input <?php if ($openhook_header_default_header) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_header_default_header" id="openhook_header_default_header" /> <label for="openhook_header_default_header" class="small"><?php _e('Remove Thesis default header', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('The default header contains your site name and tagline, but also the “Before Title” and “After Title” hooks.', 'thesis_openhook'); ?></span>
		</div>

		<div class="hook">
			<label class="head" for="openhook_before_title">Before Title:</label>
			<textarea class="resizable" id="openhook_before_title" name="openhook_before_title" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_before_title')); ?></textarea>
			<br />
			<input <?php if ($openhook_before_title_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_before_title_php" id="openhook_before_title_php" /> <label for="openhook_before_title_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_title">After Title:</label>
			<textarea class="resizable" id="openhook_after_title" name="openhook_after_title" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_title')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_title_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_title_php" id="openhook_after_title_php" /> <label for="openhook_after_title_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_before_content">Before Content:</label>
			<textarea class="resizable" id="openhook_before_content" name="openhook_before_content" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_before_content')); ?></textarea>
			<br />
			<input <?php if ($openhook_before_content_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_before_content_php" id="openhook_before_content_php" /> <label for="openhook_before_content_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_content">After Content:</label>
			<textarea class="resizable" id="openhook_after_content" name="openhook_after_content" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_content')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_content_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_content_php" id="openhook_after_content_php" /> <label for="openhook_after_content_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
			<br />
			<input <?php if ($openhook_after_content_post_navigation) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_content_post_navigation" id="openhook_after_content_post_navigation" /> <label for="openhook_after_content_post_navigation" class="small"><?php _e('Remove Thesis post navigation', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('Disables the older/newer links on the index page and archives. If you would like to move this to another hook, you can use <code>&lt;?php thesis_post_navigation(); ?&gt;</code>.', 'thesis_openhook'); ?></span>
			<br />
			<input <?php if ($openhook_after_content_prev_next_posts) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_content_prev_next_posts" id="openhook_after_content_prev_next_posts" /> <label for="openhook_after_content_prev_next_posts" class="small"><?php _e('Remove Thesis prev/next posts', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('While you can disable the previous/next post links on single pages via Thesis Options, leaving it active and removing it here allows you to add it to another hook using <code>&lt;?php thesis_prev_next_posts(); ?&gt;</code>.', 'thesis_openhook'); ?></span>
		</div>

		<div class="hook">
			<label class="head" for="openhook_before_post">Before Post:</label>
			<textarea class="resizable" id="openhook_before_post" name="openhook_before_post" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_before_post')); ?></textarea>
			<br />
			<input <?php if ($openhook_before_post_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_before_post_php" id="openhook_before_post_php" /> <label for="openhook_before_post_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_post">After Post:</label>
			<textarea class="resizable" id="openhook_after_post" name="openhook_after_post" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_post')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_post_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_post_php" id="openhook_after_post_php" /> <label for="openhook_after_post_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
			<br />
			<input <?php if ($openhook_after_post_trackback_rdf) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_post_trackback_rdf" id="openhook_after_post_trackback_rdf" /> <label for="openhook_after_post_trackback_rdf" class="small"><?php _e('Remove Thesis TrackBack <abbr title="Resource Description Framework" class="initialism">RDF</abbr>', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('Allows you to remove the TrackBack auto-discovery code from your posts. <a href="http://codex.wordpress.org/Template_Tags/trackback_rdf">More information</a> is available on the WordPress Codex.', 'thesis_openhook'); ?></span>
			<br />
			<input <?php if ($openhook_after_post_post_tags) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_post_post_tags" id="openhook_after_post_post_tags" /> <label for="openhook_after_post_post_tags" class="small"><?php _e('Remove Thesis post tags', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('While you can control the visibility of post tags via Thesis Options, if you would like to change their location, you can remove them here and add them to another hook by using <code>&lt;?php thesis_post_tags(); ?&gt;</code>.', 'thesis_openhook'); ?></span>
			<br />
			<input <?php if ($openhook_after_post_comments_link) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_post_comments_link" id="openhook_after_post_comments_link" /> <label for="openhook_after_post_comments_link" class="small"><?php _e('Remove Thesis comments link', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('This will remove the “{ Comments # }” link from the index and archive pages.', 'thesis_openhook'); ?></span>
		</div>

		<div class="hook">
			<label class="head" for="openhook_before_headline">Before Headline:</label>
			<textarea class="resizable" id="openhook_before_headline" name="openhook_before_headline" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_before_headline')); ?></textarea>
			<br />
			<input <?php if ($openhook_before_headline_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_before_headline_php" id="openhook_before_headline_php" /> <label for="openhook_before_headline_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_headline">After Headline:</label>
			<textarea class="resizable" id="openhook_after_headline" name="openhook_after_headline" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_headline')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_headline_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_headline_php" id="openhook_after_headline_php" /> <label for="openhook_after_headline_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_byline_item">Byline Item:</label>
			<textarea class="resizable" id="openhook_byline_item" name="openhook_byline_item" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_byline_item')); ?></textarea>
			<br />
			<input <?php if ($openhook_byline_item_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_byline_item_php" id="openhook_byline_item_php" /> <label for="openhook_byline_item_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_before_comment_meta">Before Comment Meta:</label>
			<textarea class="resizable" id="openhook_before_comment_meta" name="openhook_before_comment_meta" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_before_comment_meta')); ?></textarea>
			<br />
			<input <?php if ($openhook_before_comment_meta_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_before_comment_meta_php" id="openhook_before_comment_meta_php" /> <label for="openhook_before_comment_meta_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_comment_meta">After Comment Meta:</label>
			<textarea class="resizable" id="openhook_after_comment_meta" name="openhook_after_comment_meta" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_comment_meta')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_comment_meta_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_comment_meta_php" id="openhook_after_comment_meta_php" /> <label for="openhook_after_comment_meta_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_comment">After Comment:</label>
			<textarea class="resizable" id="openhook_after_comment" name="openhook_after_comment" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_comment')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_comment_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_comment_php" id="openhook_after_comment_php" /> <label for="openhook_after_comment_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_comment_form">Comment Form:</label>
			<textarea class="resizable" id="openhook_comment_form" name="openhook_comment_form" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_comment_form')); ?></textarea>
			<br />
			<input <?php if ($openhook_comment_form_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_comment_form_php" id="openhook_comment_form_php" /> <label for="openhook_comment_form_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
			<br />
			<input <?php if ($openhook_comment_form_show_subscription_checkbox) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_comment_form_show_subscription_checkbox" id="openhook_comment_form_show_subscription_checkbox" /> <label for="openhook_comment_form_show_subscription_checkbox" class="small"><?php _e('Remove comments subscription checkbox', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('If you have the Subscribe to Comments plugin installed but dislike the default location for the subscribe option, you can remove it here and place it elsewhere using <code>&lt;?php if (function_exists(\'show_subscription_checkbox\')) show_subscription_checkbox(); ?&gt;</code>.', 'thesis_openhook'); ?></span>
		</div>

		<div class="hook">
			<label class="head" for="openhook_archives_template">Archives Template:</label>
			<textarea class="resizable" id="openhook_archives_template" name="openhook_archives_template" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_archives_template')); ?></textarea>
			<br />
			<input <?php if ($openhook_archives_template_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_archives_template_php" id="openhook_archives_template_php" /> <label for="openhook_archives_template_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
			<br />
			<input <?php if ($openhook_archives_template_archives_template) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_archives_template_archives_template" id="openhook_archives_template_archives_template" /> <label for="openhook_archives_template_archives_template" class="small"><?php _e('Remove Thesis archives template', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('Thesis’ default archives template displays a list of monthly archives and categories; if you add your own archives code, you may want to remove the default.', 'thesis_openhook'); ?></span>
		</div>

		<div class="hook">
			<label class="head" for="openhook_custom_template">Custom Template:</label>
			<p><?php _e('You can specify multiple custom templates for different pages here; you can use <a href="http://codex.wordpress.org/Conditional_Tags#A_PAGE_Page">WordPress Conditional Tags</a> to ensure the code is applied to the correct pages.', 'thesis_openhook'); ?></p>
			<textarea class="resizable" id="openhook_custom_template" name="openhook_custom_template" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_custom_template')); ?></textarea>
			<br />
			<input <?php if ($openhook_custom_template_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_custom_template_php" id="openhook_custom_template_php" /> <label for="openhook_custom_template_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
			<br />
			<input <?php if ($openhook_custom_template_custom_template_sample) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_custom_template_custom_template_sample" id="openhook_custom_template_custom_template_sample" /> <label for="openhook_custom_template_custom_template_sample" class="small"><?php _e('Remove Thesis custom template sample', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('If you are creating your own custom templates for pages, you should disable Thesis’ sample custom output.', 'thesis_openhook'); ?></span>
		</div>

		<div class="hook">
			<label class="head" for="openhook_archive_info">Archive Info:</label>
			<textarea class="resizable" id="openhook_archive_info" name="openhook_archive_info" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_archive_info')); ?></textarea>
			<br />
			<input <?php if ($openhook_archive_info_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_archive_info_php" id="openhook_archive_info_php" /> <label for="openhook_archive_info_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
			<br />
			<input <?php if ($openhook_archive_info_default_archive_info) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_archive_info_default_archive_info" id="openhook_archive_info_default_archive_info" /> <label for="openhook_archive_info_default_archive_info" class="small"><?php _e('Remove Thesis default archive info', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('The default archive info appears at the top of archives and gives the name and type of the archive being viewed.', 'thesis_openhook'); ?></span>
		</div>

		<div class="hook">
			<label class="head" for="openhook_404_title">404 Title:</label>
			<textarea class="resizable" id="openhook_404_title" name="openhook_404_title" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_404_title')); ?></textarea>
			<br />
			<input <?php if ($openhook_404_title_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_404_title_php" id="openhook_404_title_php" /> <label for="openhook_404_title_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
			<br />
			<input <?php if ($openhook_404_title_404_title) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_404_title_404_title" id="openhook_404_title_404_title" /> <label for="openhook_404_title_404_title" class="small"><?php _e('Remove Thesis 404 title', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('If you’re including your own 404 page title, you will want to remove Thesis’ default title.', 'thesis_openhook'); ?></span>
		</div>

		<div class="hook">
			<label class="head" for="openhook_404_content">404 Content:</label>
			<p>Need some inspiration for what to include on your 404 error page? Check out <a href="http://rickbeckman.com/6-steps-to-a-better-404-page/">6 Steps to a Better 404 Page</a>!</p>
			<textarea class="resizable" id="openhook_404_content" name="openhook_404_content" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_404_content')); ?></textarea>
			<br />
			<input <?php if ($openhook_404_content_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_404_content_php" id="openhook_404_content_php" /> <label for="openhook_404_content_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
			<br />
			<input <?php if ($openhook_404_content_404_content) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_404_content_404_content" id="openhook_404_content_404_content" /> <label for="openhook_404_content_404_content" class="small"><?php _e('Remove Thesis 404 content', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('If you’re including your own 404 content via the box above, you will want to remove Thesis’ default 404 content.', 'thesis_openhook'); ?></span>
		</div>

		<div class="hook">
			<label class="head" for="openhook_before_sidebars">Before Sidebars:</label>
			<textarea class="resizable" id="openhook_before_sidebars" name="openhook_before_sidebars" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_before_sidebars')); ?></textarea>
			<br />
			<input <?php if ($openhook_before_sidebars_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_before_sidebars_php" id="openhook_before_sidebars_php" /> <label for="openhook_before_sidebars_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_sidebars">After Sidebars:</label>
			<textarea class="resizable" id="openhook_after_sidebars" name="openhook_after_sidebars" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_sidebars')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_sidebars_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_sidebars_php" id="openhook_after_sidebars_php" /> <label for="openhook_after_sidebars_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_multimedia_box">Multimedia Box:</label>
			<textarea class="resizable" id="openhook_multimedia_box" name="openhook_multimedia_box" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_multimedia_box')); ?></textarea>
			<br />
			<input <?php if ($openhook_multimedia_box_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_multimedia_box_php" id="openhook_multimedia_box_php" /> <label for="openhook_multimedia_box_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_multimedia_box">After Multimedia Box:</label>
			<textarea class="resizable" id="openhook_after_multimedia_box" name="openhook_after_multimedia_box" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_multimedia_box')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_multimedia_box_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_multimedia_box_php" id="openhook_after_multimedia_box_php" /> <label for="openhook_after_multimedia_box_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_before_sidebar_1">Before Sidebar 1:</label>
			<textarea class="resizable" id="openhook_before_sidebar_1" name="openhook_before_sidebar_1" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_before_sidebar_1')); ?></textarea>
			<br />
			<input <?php if ($openhook_before_sidebar_1_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_before_sidebar_1_php" id="openhook_before_sidebar_1_php" /> <label for="openhook_before_sidebar_1_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_sidebar_1">After Sidebar 1:</label>
			<textarea class="resizable" id="openhook_after_sidebar_1" name="openhook_after_sidebar_1" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_sidebar_1')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_sidebar_1_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_sidebar_1_php" id="openhook_after_sidebar_1_php" /> <label for="openhook_after_sidebar_1_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_before_sidebar_2">Before Sidebar 2:</label>
			<textarea class="resizable" id="openhook_before_sidebar_2" name="openhook_before_sidebar_2" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_before_sidebar_2')); ?></textarea>
			<br />
			<input <?php if ($openhook_before_sidebar_2_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_before_sidebar_2_php" id="openhook_before_sidebar_2_php" /> <label for="openhook_before_sidebar_2_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_sidebar_2">After Sidebar 2:</label>
			<textarea class="resizable" id="openhook_after_sidebar_2" name="openhook_after_sidebar_2" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_sidebar_2')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_sidebar_2_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_sidebar_2_php" id="openhook_after_sidebar_2_php" /> <label for="openhook_after_sidebar_2_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_before_footer">Before Footer:</label>
			<textarea class="resizable" id="openhook_before_footer" name="openhook_before_footer" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_before_footer')); ?></textarea>
			<br />
			<input <?php if ($openhook_before_footer_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_before_footer_php" id="openhook_before_footer_php" /> <label for="openhook_before_footer_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_after_footer">After Footer:</label>
			<textarea class="resizable" id="openhook_after_footer" name="openhook_after_footer" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_after_footer')); ?></textarea>
			<br />
			<input <?php if ($openhook_after_footer_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_after_footer_php" id="openhook_after_footer_php" /> <label for="openhook_after_footer_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
		</div>

		<div class="hook">
			<label class="head" for="openhook_footer">Footer:</label>
			<textarea class="resizable" id="openhook_footer" name="openhook_footer" rows="6" cols="88"><?php echo stripslashes(get_option('openhook_footer')); ?></textarea>
			<br />
			<input <?php if ($openhook_footer_php) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_footer_php" id="openhook_footer_php" /> <label for="openhook_footer_php" class="small"><?php _e('Execute <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> on this hook', 'thesis_openhook'); ?></label>
			<br />
			<input <?php if ($openhook_footer_thesis_attribution) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_footer_thesis_attribution" id="openhook_footer_thesis_attribution" /> <label for="openhook_footer_thesis_attribution" class="small"><?php _e('Remove Thesis attribution', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('Only those who purchased Thesis using the Developer’s Option are allowed to remove the Thesis attribution link. If you purchased the Personal Option and remove the Thesis attribution, be sure to include it within your own footer content (perhaps replacing it with <a href="http://diythemes.com/aff/affsignup.php?pid=58f9d50a">an affiliate link to DIYthemes</a>).', 'thesis_openhook'); ?></span>
			<br />
			<input <?php if ($openhook_footer_admin_link) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_footer_admin_link" id="openhook_footer_admin_link" /> <label for="openhook_footer_admin_link" class="small"><?php _e('Remove Thesis admin link', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('While you can toggle the admin link’s visibility in the Thesis Options, but if you leave it enabled, you can remove it from the footer and insert it into another hook using <code>&lt;?php thesis_admin_link(); ?&gt;</code>.', 'thesis_openhook'); ?></span>
			<br />
			<input <?php if ($openhook_footer_debug_info) { echo 'checked="checked"'; } ?> type="checkbox" value="1" name="openhook_footer_debug_info" id="openhook_footer_debug_info" /> <label for="openhook_footer_debug_info" class="small"><?php _e('Add debug information?', 'thesis_openhook'); ?></label>
				<span class="hook_note">&mdash; <?php _e('If you would like to keep tabs on how long <abbr title="PHP: Hypertext Preprocessor" class="initialism">PHP</abbr> takes to process your page or on how many database queries are being made, turning on the debug information will add this data to your blog’s footer, visible to administrators only. <a href="http://rickbeckman.com/use-wordpress-debug-stats-to-trim-the-fat-from-your-blog/">More information on using debug information to speed up your site.</a>', 'thesis_openhook'); ?></span>
		</div>

		<div class="tablenav">
			<div class="alignleft"><input type="submit" name="submit" value="<?php _e('Little Ass Save Button »', 'k2hookup') ?>" class="button-secondary" /></div>
			<div class="alignright"><a href="http://rickbeckman.com/" title="Kingdom Geek">Visit Kingdom Geek</a></div>
		</div>

	</form>	                      
</div>