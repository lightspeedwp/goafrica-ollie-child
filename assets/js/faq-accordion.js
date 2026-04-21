( function () {
	'use strict';

	function initFaqAccordions() {
		document.querySelectorAll( '.is-style-ga-faq-accordion .schema-faq-section' ).forEach( function ( section ) {
			var question = section.querySelector( '.schema-faq-question' );
			var answer   = section.querySelector( '.schema-faq-answer' );

			if ( ! question || ! answer ) {
				return;
			}

			question.setAttribute( 'role', 'button' );
			question.setAttribute( 'tabindex', '0' );
			question.setAttribute( 'aria-expanded', 'false' );

			function toggle() {
				var isOpen = section.classList.contains( 'is-open' );

				if ( isOpen ) {
					section.classList.remove( 'is-open' );
					section.style.removeProperty( '--ga-faq-answer-height' );
					question.setAttribute( 'aria-expanded', 'false' );
				} else {
					section.style.setProperty( '--ga-faq-answer-height', answer.scrollHeight + 'px' );
					section.classList.add( 'is-open' );
					question.setAttribute( 'aria-expanded', 'true' );
				}
			}

			question.addEventListener( 'click', toggle );

			question.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Enter' || e.key === ' ' ) {
					e.preventDefault();
					toggle();
				}
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initFaqAccordions );
	} else {
		initFaqAccordions();
	}
} )();
