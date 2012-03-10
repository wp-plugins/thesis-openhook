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
 * Determine which actions to take for each of our hooks
 */
function openhook_execute_hooks() {
	$wordpress_hooks = openhook_wordpress_hooks();
	$thesis_hooks = openhook_thesis_hooks();
	$all_hooks = array_merge( (array) $wordpress_hooks, (array) $thesis_hooks );
	$all_options = openhook_get_relevant_options();

	# Go through each of our hooks, doing stuff if needed
	foreach ( $all_hooks as $hook ) {
		# Add actions to all required hooks
		if ( isset( $all_options[ $hook[ 'name' ] ][ 'action' ] ) && ! isset ( $all_options[ $hook[ 'name' ] ][ 'disable' ] ) )
			add_action( $hook[ 'name' ], 'openhook_execute_action' );

		# Unhook actions as needed
		if ( isset( $hook[ 'unhook' ] ) ) {
			foreach ( $hook[ 'unhook' ] as $action ) {
				if ( isset( $all_options[ $hook[ 'name' ] ][ 'unhook' ][ $action[ 'name' ] ] ) ) {
					$priority = isset( $action[ 'priority' ] ) ? $action[ 'priority' ] : false;

					if ( $priority )
						remove_action( $hook[ 'name' ], $action[ 'name' ], $priority );
					else
						remove_action( $hook[ 'name' ], $action[ 'name' ] );
				}
			}
		}
	}
}

/**
 * Process an action
 */
function openhook_execute_action() {
	# Determine the current hook/filter we're acting upon & get our options to act
	$hook = current_filter();
	$options = openhook_get_relevant_options();

	# Bail out if we have neither a hook or options to work with
	if( ! $hook || ! $options )
		return;

	# Nice names for our options
	$action = $options[ $hook ][ 'action' ];
	$php = $options[ $hook ][ 'php' ];
	$shortcodes = $options[ $hook ][ 'shortcodes' ];

	# Process shortcodes if needed
	$value = $shortcodes ? do_shortcode( $action ) : $action;

	# Output our action, with or w/o PHP as needed
	if ( $php )
		eval( "?>$value<?php " );
	else
		echo $value;
}