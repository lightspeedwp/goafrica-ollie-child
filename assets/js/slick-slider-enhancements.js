/**
 * Slick slider enhancements.
 *
 * Adds two things to every Slick slider inside a `.lsx-to-slider` wrapper
 * (rendered by the Tour Operator plugin):
 *
 *   1. A mobile "peek" — a sliver of the adjacent slides is revealed so it is
 *      obvious the slider can be swiped. Implemented with Slick's own
 *      `centerMode` / `centerPadding`, re-applied whenever Slick crosses a
 *      responsive breakpoint (Slick resets its options from the original
 *      settings on each breakpoint change, so a one-off call would be lost).
 *
 *   2. A scrollbar-style progress indicator that replaces the default dots.
 *      The thumb width reflects how many slides are visible and its position
 *      reflects the scroll progress, so it never overflows no matter how many
 *      slides there are.
 *
 * Self-contained: depends only on jQuery + Slick, both of which the slider
 * already requires. Safe to copy into any project using the same markup.
 */
(function ($) {
	'use strict';

	if (typeof $ === 'undefined' || typeof $.fn === 'undefined') {
		return;
	}

	// Slider elements once Slick has initialised them.
	var SLIDER_SELECTOR = '.lsx-to-slider .slick-initialized';

	// Elements Slick will (or already did) initialise, used for delegated init.
	var INIT_SELECTOR = '.lsx-to-slider .wp-block-post-template, .lsx-to-slider .travel-information';

	var MOBILE_QUERY = window.matchMedia('(max-width: 781px)');
	var PEEK_PADDING = '14%'; // How much of the neighbouring slides to reveal.

	function getSlick($slider) {
		return $slider.hasClass('slick-initialized') ? $slider.slick('getSlick') : null;
	}

	/* --- Peek ------------------------------------------------------------- */

	function applyPeek($slider) {
		var slick = getSlick($slider);
		if (!slick) {
			return;
		}

		var wantPeek = MOBILE_QUERY.matches;
		if (slick.options.centerMode === wantPeek) {
			return; // Already in the desired state.
		}

		$slider.slick('slickSetOption', 'centerMode', wantPeek, false);
		$slider.slick('slickSetOption', 'centerPadding', wantPeek ? PEEK_PADDING : '0px', true);
	}

	/* --- Progress bar ----------------------------------------------------- */

	function getProgressBar($slider) {
		// Slick appends dots/arrows to the slider's parent; place the bar there.
		var $container = $slider.parent();
		var $bar = $container.children('.ga-slider-progress');

		if (!$bar.length) {
			$container.addClass('js-ga-progress');
			$bar = $(
				'<div class="ga-slider-progress" role="progressbar" ' +
					'aria-valuemin="0" aria-valuemax="100" aria-label="Slider position">' +
					'<span class="ga-slider-progress__fill"></span>' +
				'</div>'
			);
			$container.append($bar);
		}

		return $bar;
	}

	function updateProgress($slider) {
		var slick = getSlick($slider);
		if (!slick) {
			return;
		}

		var total = slick.slideCount;
		var perView = slick.options.slidesToShow || 1;
		var $bar = getProgressBar($slider);

		if (total <= perView) {
			$bar.attr('hidden', 'hidden');
			return;
		}

		$bar.removeAttr('hidden');

		var ratio = Math.min(1, perView / total); // Thumb width as a fraction.
		var travel = total - perView; // Number of scrollable steps.
		var progress = travel > 0 ? Math.min(1, Math.max(0, slick.currentSlide / travel)) : 0;

		$bar.children('.ga-slider-progress__fill').css({
			width: (ratio * 100) + '%',
			left: (progress * (1 - ratio) * 100) + '%'
		});
		$bar.attr('aria-valuenow', Math.round(progress * 100));
	}

	/* --- Wiring ----------------------------------------------------------- */

	function enhance($slider) {
		if ($slider.data('gaEnhanced')) {
			return;
		}
		$slider.data('gaEnhanced', true);

		applyPeek($slider);
		updateProgress($slider);

		$slider.on('afterChange.gaEnhance setPosition.gaEnhance', function () {
			updateProgress($slider);
		});

		$slider.on('breakpoint.gaEnhance', function () {
			applyPeek($slider);
			updateProgress($slider);
		});
	}

	function enhanceAll() {
		$(SLIDER_SELECTOR).each(function () {
			enhance($(this));
		});
	}

	// Catch sliders initialised after this script runs (binds before Slick init).
	$(document).on('init.gaEnhance', INIT_SELECTOR, function () {
		enhance($(this));
	});

	// Catch sliders already initialised before this script ran.
	$(enhanceAll);

	// Re-evaluate the peek when crossing the mobile breakpoint.
	function onMediaChange() {
		$(SLIDER_SELECTOR).each(function () {
			var $slider = $(this);
			applyPeek($slider);
			updateProgress($slider);
		});
	}

	if (MOBILE_QUERY.addEventListener) {
		MOBILE_QUERY.addEventListener('change', onMediaChange);
	} else if (MOBILE_QUERY.addListener) {
		MOBILE_QUERY.addListener(onMediaChange);
	}
})(window.jQuery);
