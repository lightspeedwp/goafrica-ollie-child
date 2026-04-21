<?php
/**
 * GoAfrica Ollie Child Theme functions.
 *
 * @package GoAfrica_Ollie_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue front-end scripts.
 */
function goafrica_child_enqueue_scripts() {
	wp_enqueue_script(
		'goafrica-faq-accordion',
		get_stylesheet_directory_uri() . '/assets/js/faq-accordion.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'goafrica_child_enqueue_scripts' );
