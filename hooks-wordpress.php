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
function openhook_wordpress_hooks() {
	$wordpress_hooks = array(
		'wp_head' => array(
			'name' => 'wp_head',
			'desc' => __( 'Executes within your site&rsquo;s <code>head</code> section. Useful for outputting, for example, meta tags.', 'openhook' ),
			'unhook' => array(
				'feed_links' => array(
					'name' => 'feed_links',
					'desc' => __( 'Many themes support the automatic addition of <code>link</code> tags pointing to your site&rsquo;s primary &amp; comments feeds. Disable to prevent this behavior.', 'openhook' ),
					'priority' => 2,
				),
				'feed_links_extra' => array(
					'name' => 'feed_links_extra',
					'desc' => __( 'Similar to <code>feed_links</code>, except the <code>link</code> tags output by this hook point to secondary feeds, such as category feeds.', 'openhook' ),
					'priority' => 3,
				),
				'wp_enqueue_scripts' => array(
					'name' => 'wp_enqueue_scripts',
					'desc' => __( 'Is used to queue &amp; output various scripts. Disabling this prevents most scripts from appearing in your header, allowing you to manage them all manually, if you so choose.', 'openhook' ),
					'priority' => 1,
					'advanced' => 1,
				),
				'wp_generator' => array(
					'name' => 'wp_generator',
					'desc' => sprintf( __( 'Is used to output a <code>meta</code> tag crediting WordPress as the software powering your site. Removing this is a form of <a href="%s">security through obscurity</a>.', 'openhook' ), 'http://en.wikipedia.org/wiki/Security_through_obscurity' ),
				), 
			),
		),
		'wp_footer' => array(
			'name' => 'wp_footer',
			'desc' => __( 'Executed either within the footer section (<code>div#footer</code> or <code>footer</code>) or immediately before the closing <code>html</code> tag, this hook is commonly used to output stats tracking scripts or other asyncronously loaded JavaScript.', 'openhook' ),
			'unhook' => array(
				'wp_print_footer_scripts' => array(
					'name' => 'wp_print_footer_scripts',
					'desc' => __( 'Is used to queue &amp; output barious scripts. Disabling this prevents most scripts from appearing in your footer, allowing you to manage them all manually, if you so choose.', 'openhook' ),
					'priority' => 20,
					'advanced' => 1,
				),
			),
		),
	);

	return $wordpress_hooks;
}


/*
add_action( 'wp_head',             'rsd_link'                               );
add_action( 'wp_head',             'wlwmanifest_link'                       );
add_action( 'wp_head',             'adjacent_posts_rel_link_wp_head', 10, 0 );
add_action( 'wp_head',             'locale_stylesheet'                      );
add_action( 'publish_future_post', 'check_and_publish_future_post',   10, 1 );
add_action( 'wp_head',             'noindex',                          1    );
add_action( 'wp_head',             'wp_print_styles',                  8    );
add_action( 'wp_head',             'wp_print_head_scripts',            9    );
add_action( 'wp_head',             'wp_generator'                           );
add_action( 'wp_head',             'rel_canonical'                          );
add_action( 'wp_head',             'wp_shortlink_wp_head',            10, 0 );
*/