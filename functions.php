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
 * Deregister Ollie parent button style variations not used by GoAfrica.
 */
function goafrica_child_deregister_button_styles() {
	if ( class_exists( 'WP_Theme_JSON_Resolver' ) ) {
		WP_Theme_JSON_Resolver::get_theme_data();
	}

	$variations = array( 'button-brand', 'button-brand-alt', 'button-dark', 'button-light', 'secondary-button' );
	foreach ( $variations as $variation ) {
		unregister_block_style( 'core/button', $variation );
	}
}
add_action( 'wp_loaded', 'goafrica_child_deregister_button_styles', 20 );

/**
 * Remove unwanted parent button styles from the core/button style picker.
 *
 * @param array  $args       Block type registration arguments.
 * @param string $block_type Block type name.
 * @return array
 */
function goafrica_child_filter_core_button_styles( $args, $block_type ) {
	if ( 'core/button' !== $block_type || empty( $args['styles'] ) || ! is_array( $args['styles'] ) ) {
		return $args;
	}

	$styles_to_remove = array( 'button-brand', 'button-brand-alt', 'button-dark', 'button-light', 'secondary-button' );

	$args['styles'] = array_values(
		array_filter(
			$args['styles'],
			static function( $style ) use ( $styles_to_remove ) {
				return empty( $style['name'] ) || ! in_array( $style['name'], $styles_to_remove, true );
			}
		)
	);

	return $args;
}
add_filter( 'register_block_type_args', 'goafrica_child_filter_core_button_styles', 20, 2 );

/**
 * Enqueue front-end assets.
 */
function goafrica_child_enqueue_scripts() {
	$style_path     = get_stylesheet_directory() . '/style.css';
	$functions_path = __FILE__;
	$style_version  = wp_get_theme()->get( 'Version' );

	if ( file_exists( $style_path ) ) {
		$style_version = (string) filemtime( $style_path );
	}

	if ( file_exists( $functions_path ) ) {
		$style_version .= '.' . filemtime( $functions_path );
	}

	wp_enqueue_style(
		'goafrica-ollie-child',
		get_stylesheet_uri(),
		array( 'ollie' ),
		$style_version
	);

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

/**
 * Split multi-link ga-button paragraph bindings into individual button items.
 *
 * The post-connection binding renders connected posts as one comma-separated
 * HTML string. When the paragraph uses the GA Button style we convert that
 * list into link-only content so each destination can be styled as its own pill.
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Parsed block data.
 * @return string
 */
function goafrica_child_render_ga_button_links( $block_content, $block ) {
	if ( empty( $block_content ) || empty( $block['blockName'] ) || 'core/paragraph' !== $block['blockName'] ) {
		return $block_content;
	}

	$binding = $block['attrs']['metadata']['bindings']['content'] ?? array();
	if ( empty( $binding['source'] ) || 'lsx/post-connection' !== $binding['source'] ) {
		return $block_content;
	}

	if ( false === strpos( $block_content, 'is-style-ga-button' ) ) {
		return $block_content;
	}

	preg_match_all( '/<a\b[^>]*>.*?<\/a>/si', $block_content, $matches );
	if ( empty( $matches[0] ) || count( $matches[0] ) < 2 ) {
		return $block_content;
	}

	$links_html = implode( '', array_map( 'trim', $matches[0] ) );

	$block_content = preg_replace_callback(
		'/<p\b([^>]*)class="([^"]*)"([^>]*)>/i',
		static function( $matches ) {
			$classes = preg_split( '/\s+/', trim( $matches[2] ) );
			if ( ! in_array( 'ga-button-links', $classes, true ) ) {
				$classes[] = 'ga-button-links';
			}

			return '<p' . $matches[1] . 'class="' . esc_attr( implode( ' ', array_filter( $classes ) ) ) . '"' . $matches[3] . '>';
		},
		$block_content,
		1
	);

	$block_content = preg_replace_callback(
		'/(<p\b[^>]*>)(.*?)(<\/p>)/si',
		static function( $matches ) use ( $links_html ) {
			return $matches[1] . $links_html . $matches[3];
		},
		$block_content,
		1
	);

	return $block_content;
}
add_filter( 'render_block', 'goafrica_child_render_ga_button_links', 30, 2 );

/**
 * Render bound accommodation rating paragraphs as GA button star pills.
 *
 * The Tour Operator plugin outputs PNG star images for the `rating` meta field.
 * When the paragraph uses the GA Button style we replace those images with
 * inline star characters so the pill can inherit theme colors and spacing.
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Parsed block data.
 * @return string
 */
function goafrica_child_render_ga_button_rating( $block_content, $block ) {
	if ( empty( $block_content ) || empty( $block['blockName'] ) || 'core/paragraph' !== $block['blockName'] ) {
		return $block_content;
	}

	if ( false === strpos( $block_content, 'is-style-ga-star-block' ) ) {
		return $block_content;
	}

	$binding = $block['attrs']['metadata']['bindings']['content'] ?? array();
	if ( empty( $binding['source'] ) || 'lsx/post-meta' !== $binding['source'] || empty( $binding['args']['key'] ) || 'rating' !== $binding['args']['key'] ) {
		return $block_content;
	}

	$full_stars  = preg_match_all( '/rating-star-full\.png|fa\s+fa-star(?!-o)/i', $block_content );
	$empty_stars = preg_match_all( '/rating-star-empty\.png|fa\s+fa-star-o/i', $block_content );
	$total_stars = $full_stars + $empty_stars;

	if ( 0 === $total_stars ) {
		return $block_content;
	}

	$stars_markup = '';

	for ( $index = 0; $index < $full_stars; $index++ ) {
		$stars_markup .= '<span class="ga-rating-star" aria-hidden="true">&#9733;</span>';
	}

	for ( $index = 0; $index < $empty_stars; $index++ ) {
		$stars_markup .= '<span class="ga-rating-star is-empty" aria-hidden="true">&#9733;</span>';
	}

	$block_content = preg_replace_callback(
		'/<p\b([^>]*)class="([^"]*)"([^>]*)>/i',
		static function( $matches ) {
			$classes = preg_split( '/\s+/', trim( $matches[2] ) );
			if ( ! in_array( 'ga-rating-stars', $classes, true ) ) {
				$classes[] = 'ga-rating-stars';
			}

			return '<p' . $matches[1] . 'class="' . esc_attr( implode( ' ', array_filter( $classes ) ) ) . '"' . $matches[3] . '>';
		},
		$block_content,
		1
	);

	$block_content = preg_replace(
		'/(<p\b[^>]*>)(.*?)(<\/p>)/si',
		'$1' . $stars_markup . '$3',
		$block_content,
		1
	);

	return $block_content;
}
add_filter( 'render_block', 'goafrica_child_render_ga_button_rating', 35, 2 );


