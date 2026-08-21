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


/**
 * Replace the required field legend on the contact form.
 *
 * Gravity Forms' default legend reads `"*" geeft vereiste velden aan`. The
 * asterisk keeps GF's own indicator classes so it inherits the same colour as
 * the asterisks beside the field labels.
 *
 * Scoped to form 2 (Stuur ons een bericht) via the form-specific variant of
 * `gform_required_legend`, because the copy refers to a message; other forms
 * keep the default legend.
 *
 * @param string $legend Default required field legend markup.
 * @param array  $form   Current form object.
 * @return string
 */
function goafrica_child_contact_required_legend( $legend, $form ) {
	return 'Velden met een <span class="gfield_required gfield_required_custom">*</span> zijn nodig om je bericht goed te kunnen verwerken.';
}
add_filter( 'gform_required_legend_2', 'goafrica_child_contact_required_legend', 10, 2 );

/**
 * Switch itinerary day images from the accommodation to the destination.
 *
 * `lsx_to_itinerary_thumbnail()` already knows how to resolve a day's image from the
 * connected destination — featured image first, then the destination gallery — but it
 * only takes that path when the `itinerary_use_destination_images` setting has a value.
 * Tour Operator never registers a field for that setting, so the value is injected here
 * instead of patching the plugin.
 *
 * @param array|mixed $settings Stored `lsx_to_settings` value.
 * @return array
 */
function goafrica_child_itinerary_destination_images( $settings ) {
	if ( ! is_array( $settings ) ) {
		$settings = array();
	}

	if ( ! isset( $settings['tour'] ) || ! is_array( $settings['tour'] ) ) {
		$settings['tour'] = array();
	}

	$settings['tour']['itinerary_use_destination_images'] = 'on';

	return $settings;
}
add_filter( 'option_lsx_to_settings', 'goafrica_child_itinerary_destination_images' );

/**
 * Apply the destination image setting to the already-loaded plugin instance.
 *
 * Tour Operator reads `lsx_to_settings` into its legacy object while the plugin file
 * loads, which is before a theme can filter the option, so that cached copy — the one
 * `lsx_to_itinerary_thumbnail()` actually reads — is updated as well.
 */
function goafrica_child_itinerary_destination_images_runtime() {
	if ( ! function_exists( 'tour_operator' ) ) {
		return;
	}

	$tour_operator = tour_operator();

	if ( ! $tour_operator || ! isset( $tour_operator->legacy ) ) {
		return;
	}

	$tour_operator->legacy->options = goafrica_child_itinerary_destination_images( $tour_operator->legacy->options );
}
add_action( 'init', 'goafrica_child_itinerary_destination_images_runtime', 20 );

/**
 * Track the image size Tour Operator is currently resolving.
 *
 * `lsx_to_itinerary_thumbnail_src` does not pass the size along, so it is captured from
 * the size filter that runs earlier in the same call.
 *
 * @param string|array|null $size Size being resolved, or null to read the stored value.
 * @return string|array
 */
function goafrica_child_itinerary_image_size( $size = null ) {
	static $current = 'medium';

	if ( null !== $size ) {
		$current = $size;
	}

	return $current;
}
add_filter( 'lsx_to_itinerary_thumbnail_size', 'goafrica_child_itinerary_image_size' );

/**
 * Fall back to the accommodation image when the day's destination has none.
 *
 * Destinations are less consistently illustrated than accommodations, so without this
 * a day whose destination has neither a featured image nor a gallery would drop to the
 * generic tour placeholder. The plugin's own accommodation walk is repeated here — same
 * order, same used-image bookkeeping — so repeated days still cycle through the gallery
 * rather than showing the same photo twice.
 *
 * @param string|false $thumbnail_src Image URL resolved by the plugin, false or '' when none was found.
 * @param int          $index         Current itinerary day.
 * @param int          $count         Total itinerary days.
 * @return string|false
 */
function goafrica_child_itinerary_accommodation_fallback( $thumbnail_src, $index, $count ) {
	global $tour_itinerary;

	if ( ! $tour_itinerary || empty( $tour_itinerary->itinerary ) || ! is_array( $tour_itinerary->itinerary ) ) {
		return $thumbnail_src;
	}

	$size = goafrica_child_itinerary_image_size();

	if ( false !== $thumbnail_src && '' !== $thumbnail_src ) {
		// On the last day the plugin has already substituted the tour's own featured
		// image. Treat that as "nothing found" so an accommodation photo still wins,
		// which is the order that applied before the destination switch.
		$tour_image = wp_get_attachment_image_src( get_post_thumbnail_id(), $size );

		if ( $index !== $count || ! is_array( $tour_image ) || $tour_image[0] !== $thumbnail_src ) {
			return $thumbnail_src;
		}
	}

	$accommodation_ids = $tour_itinerary->itinerary['accommodation_to_tour'] ?? array();

	if ( ! is_array( $accommodation_ids ) ) {
		$accommodation_ids = '' === $accommodation_ids ? array() : array( $accommodation_ids );
	}

	foreach ( $accommodation_ids as $accommodation_id ) {
		$tour_itinerary->register_current_gallery( $accommodation_id, 'accommodation_to_tour' );

		$image_id = get_post_thumbnail_id( $accommodation_id );

		if ( empty( $image_id ) || $tour_itinerary->is_image_used( $image_id ) ) {
			$image_id = $tour_itinerary->find_next_image( $accommodation_id );
		}

		if ( empty( $image_id ) ) {
			continue;
		}

		$image = wp_get_attachment_image_src( $image_id, $size );

		if ( is_array( $image ) ) {
			$tour_itinerary->save_used_image( $image_id );
			return $image[0];
		}
	}

	return $thumbnail_src;
}
add_filter( 'lsx_to_itinerary_thumbnail_src', 'goafrica_child_itinerary_accommodation_fallback', 10, 3 );


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

/**
 * The text shown in place of a tour price when there is no amount to show.
 *
 * @return string
 */
function goafrica_child_price_on_request_label() {
	return _x( 'Op aanvraag', 'shown instead of a tour price when none is set', 'goafrica-ollie-child' );
}

/**
 * Work out what a tour's price field should display.
 *
 * Returns null when the field holds a normal amount, so Tour Operator's own
 * currency formatting stands. Otherwise returns the text to show in its place:
 * whatever was typed when it carries no digits, and the on-request label when
 * nothing was entered.
 *
 * Both cases are handled here because the field arrives in three shapes. The
 * WETU importer leaves some tours with no price at all and others with a lone
 * space, and prices that are not yet fixed are typed in as words. Treating
 * "no digits" and "nothing at all" the same way means a re-import cannot
 * reintroduce a broken price, and nobody has to remember to type anything.
 *
 * @param int $post_id Post being rendered.
 * @return string|null Text to display, or null to leave the price alone.
 */
function goafrica_child_price_on_request_text( $post_id ) {
	if ( empty( $post_id ) || 'tour' !== get_post_type( $post_id ) ) {
		return null;
	}

	$price = get_post_meta( $post_id, 'price', true );

	if ( ! is_scalar( $price ) ) {
		$price = '';
	}

	// The importer and the WYSIWYG editors both leave non-breaking spaces behind,
	// so normalise them before deciding whether anything was actually entered.
	$price = trim( str_replace( array( '&nbsp;', "\xc2\xa0" ), ' ', (string) $price ) );

	if ( '' === $price ) {
		return goafrica_child_price_on_request_label();
	}

	if ( ! preg_match( '/\d/', $price ) ) {
		// Only pass the value through when there is a word in it. The importer also
		// leaves stray punctuation behind — a lone '.' is the common one — which is
		// neither an amount nor something worth showing a visitor.
		return preg_match( '/\p{L}/u', $price ) ? $price : goafrica_child_price_on_request_label();
	}

	return null;
}

/**
 * Keep the price block on the page when there is no amount to show.
 *
 * Tour Operator's `maybe_hide_varitaion()` empties any `lsx-*-wrapper` group whose
 * meta field has no value, which on a tour takes the "Vanaf:" label and the price
 * paragraph with it — so the on-request text would never be reached.
 *
 * Both spellings are hooked deliberately: the plugin shipped this filter as
 * `..._varitaion_override` before the name was corrected, and which one the
 * installed version applies cannot be determined from the theme.
 *
 * @param bool   $has_values Whether the wrapper's fields have values.
 * @param string $meta_key   The meta key being checked.
 * @param mixed  $value      The raw meta value.
 * @param int    $post_id    Post being rendered.
 * @return bool
 */
function goafrica_child_always_show_tour_price( $has_values, $meta_key, $value = '', $post_id = 0 ) {
	if ( 'price' !== $meta_key ) {
		return $has_values;
	}

	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	if ( 'tour' !== get_post_type( $post_id ) ) {
		return $has_values;
	}

	return true;
}
add_filter( 'lsx_to_maybe_hide_variation_override', 'goafrica_child_always_show_tour_price', 10, 4 );
add_filter( 'lsx_to_maybe_hide_varitaion_override', 'goafrica_child_always_show_tour_price', 10, 4 );

/**
 * Show the on-request text in place of a price that has no amount.
 *
 * This runs on the rendered block rather than on the bound value because two
 * separate things have to be undone, and both of them happen during rendering:
 *
 * - `Tour::price_filter()` strips every non-digit from the value and puts the
 *   remainder through `number_format()`, so a tour priced "op aanvraag" and one
 *   holding a lone space both arrive here as `€0.00`.
 * - `Bindings::render_paragraph_prefix_block()` prepends the block's `prefix`
 *   attribute at priority 20, which is where the bare `€` on the tour cards and
 *   the "From:" on the tour modal come from.
 *
 * Replacing the paragraph's contents after both have run settles them together
 * and drops the prefix along with the amount, which is why this sits at 25.
 * It also puts the substitution after core has run the bound value through
 * `wp_kses_post()`, so the text is not filtered twice.
 *
 * The price is read from the post rather than from the rendered value, because by
 * this point the value has already been reduced to a formatted number.
 *
 * @param string   $block_content Rendered block content.
 * @param array    $block         Parsed block data.
 * @param WP_Block $block_obj     Block instance.
 * @return string
 */
function goafrica_child_render_price_on_request( $block_content, $block, $block_obj = null ) {
	if ( empty( $block_content ) || empty( $block['blockName'] ) || 'core/paragraph' !== $block['blockName'] ) {
		return $block_content;
	}

	$binding = $block['attrs']['metadata']['bindings']['content'] ?? array();

	// The tour cards bind through core's own source, the fast facts and the modal
	// through the plugin's, and both end up in the same broken state.
	if ( empty( $binding['source'] ) || ! in_array( $binding['source'], array( 'lsx/post-meta', 'core/post-meta' ), true ) ) {
		return $block_content;
	}

	$key = isset( $binding['args']['key'] ) ? str_replace( '-', '_', $binding['args']['key'] ) : '';

	if ( 'price' !== $key ) {
		return $block_content;
	}

	// `core/post-meta` declares `postId` as context, so a bound paragraph inside a
	// query loop carries the card's own post. The plugin's source declares none,
	// where the current post in the loop is the same thing.
	$post_id = is_object( $block_obj ) && ! empty( $block_obj->context['postId'] ) ? $block_obj->context['postId'] : get_the_ID();

	$text = goafrica_child_price_on_request_text( $post_id );

	if ( null === $text ) {
		return $block_content;
	}

	return preg_replace_callback(
		'#(<p\b[^>]*>)(.*?)(</p>)#si',
		static function( $matches ) use ( $text ) {
			return $matches[1] . esc_html( $text ) . $matches[3];
		},
		$block_content,
		1
	);
}
add_filter( 'render_block', 'goafrica_child_render_price_on_request', 25, 3 );

/**
 * Return the Tour Operator modals instance, if it is there to work with.
 *
 * @return object|null
 */
function goafrica_child_get_modals_instance() {
	if ( ! function_exists( 'tour_operator' ) ) {
		return null;
	}

	$tour_operator = tour_operator();

	if ( ! is_object( $tour_operator ) || empty( $tour_operator->classes['modals'] ) ) {
		return null;
	}

	$modals = $tour_operator->classes['modals'];

	if ( ! is_object( $modals ) || ! method_exists( $modals, 'output_modal_contents' ) || ! property_exists( $modals, 'modal_contents' ) ) {
		return null;
	}

	return $modals;
}

/**
 * Print the Tour Operator modal template parts as rendered.
 *
 * Replaces `lsx\frontend\Modals::output_modal_contents()`, which puts each
 * template part's already-rendered block HTML through `wpautop()` and then
 * `wp_kses()` before printing it. Both are destructive on markup that is not
 * post content, and the enquiry modal shows it plainly:
 *
 * - `wpautop()` wraps the two `.gform-grid-col` spans of the Gravity Form's name
 *   field in paragraphs, so they drop out of the flex row and lose both the
 *   column gap and the compensation for the row's negative inline margin, and it
 *   gives every hidden input in the form footer a trailing `<br />`, which is
 *   the run of empty space under the submit button.
 * - `wp_kses()` replaces core's `button` entry with one listing only `class`,
 *   `aria-label` and `data-close`, which takes `type="submit"` and
 *   `id="gform_submit_button_1"` off the button Gravity Forms' own script binds
 *   to and leaves the datepicker toggle with no `type`, so it defaults to
 *   submitting the form. It also entity-encodes the contents of the inline
 *   `<script>` Gravity Forms bootstraps itself with — `=>` becomes `=&gt;` —
 *   and a `<script>` element's contents are raw text, so the browser never
 *   decodes it back and the block fails to parse.
 *
 * None of it shows in the editor, which renders the template part directly.
 *
 * Printing the content as rendered is what core itself does for a template
 * part: this is `do_blocks()` output from a `wp_template_part` post, which only
 * a user with `edit_theme_options` can author, and the same form rendered
 * anywhere else on the page goes out unescaped. Only the attributes built here
 * are escaped.
 *
 * `Modals::output_modal_ids()` is left alone: it renders the connected-item
 * modals and calls neither function.
 *
 * @return void
 */
function goafrica_child_output_modal_contents() {
	$modals = goafrica_child_get_modals_instance();

	if ( null === $modals || empty( $modals->modal_contents ) ) {
		return;
	}

	wp_enqueue_script( 'lsx-to-modals' );

	foreach ( $modals->modal_contents as $modal_id => $content ) {
		?>
		<dialog id="<?php echo esc_attr( $modal_id ); ?>" class="wp-block-hm-popup" data-trigger="click" data-expiry="7" data-backdrop-opacity="0.75" tabindex="-1">
			<div style="position:relative;">
				<div class="wp-block-template-part">
					<?php echo $content; // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Rendered block output from an administrator-authored template part. ?>
				</div>
				<button class="wp-block-hm-popup__close" type="button" aria-label="<?php esc_attr_e( 'Close', 'goafrica-ollie-child' ); ?>" data-close>
					<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M8 24.5L24 8.5M8 8.5L24 24.5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
			</div>
		</dialog>
		<?php
	}
}

/**
 * Swap the plugin's modal content output for the one above.
 *
 * `Modals::init()` hooks its version on `wp_loaded` at 10, so this runs at 20.
 * If the hook is not where it was expected the swap is skipped and the plugin's
 * own output stands, so a plugin update degrades to today's behaviour rather
 * than to a missing modal.
 *
 * @return void
 */
function goafrica_child_replace_modal_output() {
	$modals = goafrica_child_get_modals_instance();

	if ( null === $modals ) {
		return;
	}

	if ( remove_action( 'wp_footer', array( $modals, 'output_modal_contents' ), 11 ) ) {
		add_action( 'wp_footer', 'goafrica_child_output_modal_contents', 11 );
	}
}
add_action( 'wp_loaded', 'goafrica_child_replace_modal_output', 20 );
