<?php
/*
 * Plugin Name: The OpenHook Customizations Manager
 * Plugin URI: http://rickbeckman.org/openhook/
 * Description: Easy access to the assorted hooks available in WordPress & Thesis.
 * Version: 3.3.2
 * Author: Rick Beckman
 * Author URI: http://rickbeckman.org/
 * License: GNU General Public License v2.0 (or later)
 * License URI: http://www.opensource.org/licenses/gpl-license.php
*/

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
 * Define assorted constants
 */
define( 'OPENHOOK_SETTINGS_GENERAL', 'openhook_settings_general' );
define( 'OPENHOOK_SETTINGS_THESIS', 'openhook_settings_thesis' );
define( 'OPENHOOK_SETTINGS_WORDPRESS', 'openhook_settings_wordpress' );
define( 'OPENHOOK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OPENHOOK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'OPENHOOK_LANG_DIR', OPENHOOK_PLUGIN_DIR . 'languages/' );

if ( defined( 'OPENHOOK_SAFEGUARD' ) )
	wp_die( sprintf( __( 'The constant %s is defined which creates a security risk.', 'openhook' ), '<code>OPENHOOK_SAFEGUARD</code>' ) );

/**
 * Enable localization
 */
load_plugin_textdomain( 'openhook', false, OPENHOOK_LANG_DIR );

/**
 * Include required files
 */
include( OPENHOOK_PLUGIN_DIR . 'functions-actions.php' );
include( OPENHOOK_PLUGIN_DIR . 'functions-options.php' );
include( OPENHOOK_PLUGIN_DIR . 'hooks-thesis.php' );
include( OPENHOOK_PLUGIN_DIR . 'hooks-wordpress.php' );
if ( is_admin() )
	include( OPENHOOK_PLUGIN_DIR . 'functions-admin.php' );

# The meat & potatoes of OpenHook processes on the frontend only
if ( ! is_admin() ) {
	# Process our hooks
	add_action( 'init', 'openhook_execute_hooks' );

	# Handle hook visualization
	$options = get_option( 'openhook_general' );
	if ( isset( $options[ 'visualize_hooks' ] ) && $options[ 'visualize_hooks' ] )
		add_action( 'init', 'openhook_setup_hook_visualization' );
}