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
	var PEEK_PADDING = '10%'; // How much of the neighbouring slides to reveal.
	var FALLBACK_DESKTOP_SLIDES = 3;
	var FALLBACK_SELECTOR = '.lsx-to-slider .travel-information';

	function storeSlick($slider, slick) {
		if ($slider.length && slick) {
			$slider[0]._gaSlick = slick;
		}
	}

	function getSlick($slider) {
		if (!$slider.length) {
			return null;
		}

		if ($slider[0]._gaSlick) {
			return $slider[0]._gaSlick;
		}

		if (!$slider.hasClass('slick-initialized')) {
			return null;
		}

		try {
			var slick = $slider.slick('getSlick');
			storeSlick($slider, slick);
			return slick;
		} catch (error) {
			return null;
		}
	}

	function getStateFromDom($slider) {
		var $slides = $slider.find('.slick-slide').not('.slick-cloned');
		if (!$slides.length) {
			return null;
		}

		var $activeSlides = $slider.find('.slick-slide.slick-active').not('.slick-cloned');
		var currentIndex = parseInt($slider.find('.slick-slide.slick-current').attr('data-slick-index'), 10);

		if (isNaN(currentIndex)) {
			currentIndex = parseInt($activeSlides.first().attr('data-slick-index'), 10);
		}

		return {
			total: $slides.length,
			perView: Math.max(1, $activeSlides.length || 1),
			currentSlide: isNaN(currentIndex) ? 0 : Math.max(0, currentIndex)
		};
	}

	function getSliderState($slider) {
		var slick = getSlick($slider);
		if (slick) {
			return {
				slick: slick,
				total: slick.slideCount,
				perView: slick.options.slidesToShow || 1,
				currentSlide: slick.currentSlide || 0
			};
		}

		return getStateFromDom($slider);
	}

	function setSlickOption($slider, slick, option, value, refresh) {
		if (slick && typeof slick.slickSetOption === 'function') {
			slick.slickSetOption(option, value, refresh);
			storeSlick($slider, slick);
			return true;
		}

		try {
			$slider.slick('slickSetOption', option, value, refresh);
			return true;
		} catch (error) {
			return false;
		}
	}

	/* --- Peek ------------------------------------------------------------- */

	function applyPeek($slider, slick) {
		slick = slick || getSlick($slider);
		if (!slick) {
			return;
		}

		var wantPeek = MOBILE_QUERY.matches;
		if (slick.options.centerMode === wantPeek) {
			return; // Already in the desired state.
		}

		if (!setSlickOption($slider, slick, 'centerMode', wantPeek, false)) {
			return;
		}

		setSlickOption($slider, slick, 'centerPadding', wantPeek ? PEEK_PADDING : '0px', true);
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
		var state = getSliderState($slider);
		if (!state) {
			return;
		}

		var total = state.total;
		var perView = state.perView;
		var $bar = getProgressBar($slider);

		if (total <= perView) {
			$bar.attr('hidden', 'hidden');
			return;
		}

		$bar.removeAttr('hidden');

		var ratio = Math.min(1, perView / total); // Thumb width as a fraction.
		var travel = total - perView; // Number of scrollable steps.
		var progress = travel > 0 ? Math.min(1, Math.max(0, state.currentSlide / travel)) : 0;

		$bar.children('.ga-slider-progress__fill').css({
			width: (ratio * 100) + '%',
			left: (progress * (1 - ratio) * 100) + '%'
		});
		$bar.attr('aria-valuenow', Math.round(progress * 100));
	}

	/* --- Wiring ----------------------------------------------------------- */

	function refreshEnhancement($slider, slick) {
		storeSlick($slider, slick);
		applyPeek($slider, slick);
		updateProgress($slider);
	}

	function bindEnhancementEvents($slider) {
		if ($slider.data('gaEnhancementEventsBound')) {
			return;
		}

		$slider.data('gaEnhancementEventsBound', true);

		$slider.on('init.gaEnhance reInit.gaEnhance setPosition.gaEnhance afterChange.gaEnhance breakpoint.gaEnhance', function (event, slick) {
			refreshEnhancement($slider, slick || null);
		});
	}

	function getDesktopSlidesToShow($slider) {
		var classes = $slider.attr('class') || '';
		var match = classes.match(/columns-(\d+)/);
		var slideCount = $slider.children().length || 1;

		if (match) {
			return Math.max(1, Math.min(slideCount, parseInt(match[1], 10)));
		}

		if ($slider.is('.travel-information')) {
			return Math.max(1, Math.min(slideCount, 3));
		}

		return Math.max(1, Math.min(slideCount, FALLBACK_DESKTOP_SLIDES));
	}

	function getResponsiveSettings(slidesToShow) {
		var tabletSlides = Math.max(1, Math.min(slidesToShow, 2));

		return [
			{
				breakpoint: 1228,
				settings: {
					slidesToShow: tabletSlides,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 1028,
				settings: {
					slidesToShow: tabletSlides,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 782,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			}
		];
	}

	function initFallbackSlider($slider) {
		if (!$slider.length || $slider.hasClass('slick-initialized') || typeof $slider.slick !== 'function') {
			return;
		}

		var slidesToShow = getDesktopSlidesToShow($slider);

		$slider.slick({
			appendArrows: $slider.parent(),
			appendDots: $slider.parent(),
			arrows: true,
			dots: true,
			infinite: $slider.children().length > slidesToShow,
			slidesToScroll: 1,
			slidesToShow: slidesToShow,
			responsive: getResponsiveSettings(slidesToShow)
		});
	}

	function initFallbackSliders() {
		$(FALLBACK_SELECTOR).each(function () {
			initFallbackSlider($(this));
		});
	}

	function enhance($slider, slick) {
		bindEnhancementEvents($slider);

		if ($slider.data('gaEnhanced')) {
			refreshEnhancement($slider, slick || null);
			return;
		}

		$slider.data('gaEnhanced', true);
		refreshEnhancement($slider, slick || null);
	}

	function enhanceAll() {
		$(SLIDER_SELECTOR).each(function () {
			enhance($(this));
		});
	}

	// Catch sliders initialised after this script runs (binds before Slick init).
	$(document).on('init.gaEnhance reInit.gaEnhance', INIT_SELECTOR, function (event, slick) {
		enhance($(this), slick);
	});

	// Catch sliders already initialised before this script ran.
	$(enhanceAll);
	$(window).on('load.gaEnhance', initFallbackSliders);

	// Re-evaluate the peek when crossing the mobile breakpoint.
	function onMediaChange() {
		$(SLIDER_SELECTOR).each(function () {
			var $slider = $(this);
			refreshEnhancement($slider, null);
		});
	}

	if (MOBILE_QUERY.addEventListener) {
		MOBILE_QUERY.addEventListener('change', onMediaChange);
	} else if (MOBILE_QUERY.addListener) {
		MOBILE_QUERY.addListener(onMediaChange);
	}
})(window.jQuery);
