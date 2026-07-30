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
	wp_enqueue_style(
		'goafrica-ollie-child',
		get_stylesheet_uri(),
		array( 'ollie' ),
		wp_get_theme()->get( 'Version' )
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


function goafrica_child_use_destination_images() {
	return true;
}
//add_filter( 'lsx_to_itinerary_use_destination_images', 'goafrica_child_use_destination_images' );


/**
 * Alters the titles
 *
 * @param array $titles
 * @return void
 */
function goafrica_travel_info_modal_titles( $titles ) {
	$titles['banking'] = 'Bankieren';
	$titles['dress'] = 'Kleding';
	return $titles;
}
add_filter( 'lsx_to_travel_info_modal_titles', 'goafrica_travel_info_modal_titles', 10, 1 );


/**
 * Alters the itinerary titles
 *
 * @param string $title
 * @return string
 */
function goafrica_travel_itinerary_titles_transform( $title ) {
	$title = str_replace( 'Day', 'Dag', $title );
	return $title;
}
add_filter( 'lsx_to_itinerary_title', 'goafrica_travel_itinerary_titles_transform', 10, 1 );



/**
 * Alters the readless
 *
 * @param array $title
 * @return array
 */
function goafrica_travel_js_strings( $js_strings ) {
	$js_strings['read_less'] = 'Lees minder';
	return $js_strings;
}
add_filter( 'lsx_to_js_strings', 'goafrica_travel_js_strings', 10, 1 );


/**
 * Alters the readless
 *
 * @param array $title
 * @return array
 */
function goafrica_wetu_language( $lang ) {
	$lang .= '&lang=nl';
	return $lang;
}
add_filter( 'lsx_wetu_language', 'goafrica_wetu_language', 10, 1 );


/**
 * Split a WYSIWYG meta value into individual list items.
 *
 * The Tour Operator `included` / `not_included` fields are WYSIWYG editors and the
 * WETU importer stores whatever the API returns, so the saved markup takes several
 * shapes: authored `<ul>` lists, one `<p>` per item, and `<br>`-separated runs,
 * frequently with a literal bullet character typed in front of each item.
 *
 * @param string $value Raw meta value.
 * @return array<int, string> Cleaned list items, empty when nothing usable is found.
 */
function goafrica_child_split_meta_list( $value ) {
	// Normalise the non-breaking spaces WYSIWYG editors leave behind so trimming works.
	$value = str_replace( array( '&nbsp;', "\xc2\xa0" ), ' ', $value );

	if ( preg_match_all( '#<li\b[^>]*>(.*?)</li>#si', $value, $matches ) ) {
		// Already a list: take the items exactly as they were authored.
		$items = $matches[1];
	} else {
		// Otherwise treat every block boundary and line break as an item separator.
		$items = preg_split( '/\R/', preg_replace( '#<br\s*/?>|</p>|</div>|<hr\s*/?>#i', "\n", $value ) );
	}

	$list = array();

	foreach ( $items as $item ) {
		// Drop leftover block-level tags but keep inline markup such as links and bold.
		$item = preg_replace( '#</?(?:p|div|ul|ol|li)\b[^>]*>#i', '', $item );

		// Strip a leading bullet glyph so it does not double up with the CSS icon.
		$unbulleted = preg_replace( '/^[\s\x{2022}\x{00b7}\x{2023}\x{25aa}\x{25e6}\x{2043}\x{2013}\x{2014}\-\*\+]+/u', '', $item );
		if ( null !== $unbulleted ) {
			$item = $unbulleted;
		}

		$item = trim( $item );

		if ( '' === $item || '' === trim( wp_strip_all_tags( $item ) ) ) {
			continue;
		}

		$list[] = wp_kses_post( $item );
	}

	return $list;
}

/**
 * Render the included / not_included meta fields as icon lists.
 *
 * Icons are applied in CSS from the `lsx-meta-list` classes rather than injected
 * here, because core runs the bound value through `wp_kses_post()` which strips SVG.
 *
 * @param string $return_html The formatted HTML output.
 * @param string $meta_key    The meta key being queried.
 * @param mixed  $value       The raw meta value.
 * @param string $before      HTML before content.
 * @param string $after       HTML after content.
 * @return string
 */
function goafrica_child_render_meta_lists( $return_html, $meta_key, $value, $before, $after ) {
	if ( ! in_array( $meta_key, array( 'included', 'not_included' ), true ) ) {
		return $return_html;
	}

	if ( ! is_scalar( $value ) || '' === trim( (string) $value ) ) {
		return $return_html;
	}

	$items = goafrica_child_split_meta_list( (string) $value );

	if ( empty( $items ) ) {
		return $return_html;
	}

	$output = '<ul class="lsx-meta-list lsx-' . esc_attr( $meta_key ) . '-list">';

	foreach ( $items as $item ) {
		$output .= '<li>' . $item . '</li>';
	}

	$output .= '</ul>';

	return $before . $output . $after;
}
add_filter( 'lsx_to_custom_field_query', 'goafrica_child_render_meta_lists', 10, 5 );

/**
 * Lift the generated meta list out of its bound paragraph wrapper.
 *
 * Block bindings replace the inner HTML of the `<p>`, which would leave a `<ul>`
 * nested inside a paragraph. Browsers hoist the list out and strand the
 * paragraph's colour, typography and spacing on an empty `<p>`, so the list is
 * promoted to the paragraph's place and inherits its presentation attributes.
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Parsed block data.
 * @return string
 */
function goafrica_child_unwrap_meta_list( $block_content, $block ) {
	if ( empty( $block_content ) || empty( $block['blockName'] ) || 'core/paragraph' !== $block['blockName'] ) {
		return $block_content;
	}

	if ( false === strpos( $block_content, 'lsx-meta-list' ) ) {
		return $block_content;
	}

	$binding = $block['attrs']['metadata']['bindings']['content'] ?? array();
	if ( empty( $binding['source'] ) || 'lsx/post-meta' !== $binding['source'] ) {
		return $block_content;
	}

	if ( ! preg_match( '#<ul\b[^>]*\bclass="[^"]*lsx-meta-list[^"]*"[^>]*>.*</ul>#si', $block_content, $list ) ) {
		return $block_content;
	}

	$unwrapped = new WP_HTML_Tag_Processor( $list[0] );
	if ( ! $unwrapped->next_tag( array( 'tag_name' => 'UL' ) ) ) {
		return $block_content;
	}

	$paragraph = new WP_HTML_Tag_Processor( $block_content );
	if ( $paragraph->next_tag( array( 'tag_name' => 'P' ) ) ) {
		$classes = trim( (string) $paragraph->get_attribute( 'class' ) );
		$style   = (string) $paragraph->get_attribute( 'style' );

		foreach ( array_filter( preg_split( '/\s+/', $classes ) ) as $class ) {
			$unwrapped->add_class( $class );
		}

		if ( '' !== $style ) {
			$unwrapped->set_attribute( 'style', $style );
		}
	}

	return $unwrapped->get_updated_html();
}
add_filter( 'render_block', 'goafrica_child_unwrap_meta_list', 20, 2 );
