/**
 * Carousel Block (Swiper) enhancements.
 *
 * Adds two things to every `.wp-block-cb-carousel-v2` block:
 *
 *   1. A mobile "peek" — a sliver of the next slide is revealed so it is
 *      obvious the carousel can be swiped. Implemented by nudging Swiper's
 *      `slidesPerView` to a fractional value on mobile, re-applied whenever
 *      Swiper changes breakpoint so the block plugin's own responsive config
 *      cannot undo it.
 *
 *   2. A scrollbar-style progress indicator that replaces the default dots.
 *      The thumb width reflects how many slides are visible and its position
 *      tracks Swiper's live `progress`, so it never overflows no matter how
 *      many slides there are.
 *
 * Decoupled from the block plugin: it reads the live Swiper instance attached
 * to the `.swiper` element, so no plugin edits are required. Safe to copy into
 * any project that renders Swiper-based carousels with the same block markup.
 */
(function () {
	'use strict';

	var BLOCK_SELECTOR = '.wp-block-cb-carousel-v2';
	var MOBILE_QUERY = window.matchMedia('(max-width: 781px)');
	var PEEK_VIEW = 1.10; // slidesPerView on mobile (reveals the next slide).
	var PEEK_GAP = 14; // Minimum spaceBetween on mobile, in px.

	function getSwiper(block) {
		var el = block.matches('.swiper') ? block : block.querySelector('.swiper');
		return el && el.swiper ? el.swiper : null;
	}

	function slidesPerView(swiper) {
		var spv = swiper.params.slidesPerView;
		if (typeof spv === 'number') {
			return spv;
		}
		if (typeof swiper.slidesPerViewDynamic === 'function') {
			return swiper.slidesPerViewDynamic();
		}
		return 1;
	}

	/* --- Progress bar ----------------------------------------------------- */

	function getProgressBar(block) {
		var bar = block.querySelector(':scope > .ga-slider-progress');

		if (!bar) {
			block.classList.add('js-ga-progress');
			bar = document.createElement('div');
			bar.className = 'ga-slider-progress';
			bar.setAttribute('role', 'progressbar');
			bar.setAttribute('aria-valuemin', '0');
			bar.setAttribute('aria-valuemax', '100');
			bar.setAttribute('aria-label', 'Slider position');

			var fill = document.createElement('span');
			fill.className = 'ga-slider-progress__fill';
			bar.appendChild(fill);
			block.appendChild(bar);
		}

		return bar;
	}

	function updateProgress(block, swiper) {
		var bar = getProgressBar(block);
		var total = swiper.slides ? swiper.slides.length : 0;
		var perView = slidesPerView(swiper);

		if (!total || total <= perView) {
			bar.hidden = true;
			return;
		}
		bar.hidden = false;

		var ratio = Math.min(1, perView / total); // Thumb width as a fraction.
		var progress = Math.min(1, Math.max(0, swiper.progress));

		var fill = bar.querySelector('.ga-slider-progress__fill');
		fill.style.width = (ratio * 100) + '%';
		fill.style.left = (progress * (1 - ratio) * 100) + '%';
		bar.setAttribute('aria-valuenow', String(Math.round(progress * 100)));
	}

	/* --- Peek ------------------------------------------------------------- */

	function applyPeek(block, swiper) {
		if (typeof block._gaBaseView === 'undefined') {
			block._gaBaseView = swiper.params.slidesPerView;
			block._gaBaseGap = swiper.params.spaceBetween || 0;
		}

		var mobile = MOBILE_QUERY.matches;
		var targetView = mobile ? PEEK_VIEW : block._gaBaseView;
		var targetGap = mobile ? Math.max(block._gaBaseGap, PEEK_GAP) : block._gaBaseGap;

		if (swiper.params.slidesPerView === targetView && swiper.params.spaceBetween === targetGap) {
			return;
		}

		swiper.params.slidesPerView = targetView;
		swiper.params.spaceBetween = targetGap;
		swiper.update();
	}

	/* --- Wiring ----------------------------------------------------------- */

	function enhance(block) {
		var swiper = getSwiper(block);
		if (!swiper) {
			return false; // Swiper not ready yet.
		}
		if (block._gaEnhanced) {
			return true;
		}
		block._gaEnhanced = true;

		applyPeek(block, swiper);
		updateProgress(block, swiper);

		swiper.on('progress', function () {
			updateProgress(block, swiper);
		});
		swiper.on('slidesLengthChange', function () {
			updateProgress(block, swiper);
		});
		swiper.on('breakpoint', function () {
			applyPeek(block, swiper);
			updateProgress(block, swiper);
		});

		return true;
	}

	function enhanceAll() {
		var blocks = document.querySelectorAll(BLOCK_SELECTOR);
		var allReady = true;
		blocks.forEach(function (block) {
			if (!enhance(block)) {
				allReady = false;
			}
		});
		return blocks.length === 0 || allReady;
	}

	// Swiper may initialise after this script; poll briefly until it exists.
	function init(attempt) {
		if (enhanceAll() || attempt > 60) {
			return;
		}
		requestAnimationFrame(function () {
			init(attempt + 1);
		});
	}

	function onMediaChange() {
		document.querySelectorAll(BLOCK_SELECTOR).forEach(function (block) {
			var swiper = getSwiper(block);
			if (swiper) {
				applyPeek(block, swiper);
				updateProgress(block, swiper);
			}
		});
	}

	if (document.readyState !== 'loading') {
		init(0);
	} else {
		document.addEventListener('DOMContentLoaded', function () {
			init(0);
		});
	}

	if (MOBILE_QUERY.addEventListener) {
		MOBILE_QUERY.addEventListener('change', onMediaChange);
	} else if (MOBILE_QUERY.addListener) {
		MOBILE_QUERY.addListener(onMediaChange);
	}
})();
