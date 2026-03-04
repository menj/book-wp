/**
 * Book WP — Main JavaScript
 * Version: 2.0.0
 * License: GPL-2.0-or-later
 *
 * No jQuery dependency. Vanilla JS only.
 */

(function () {
	'use strict';

	/**
	 * Keyboard navigation: allow Enter/Space on focusable non-button elements
	 * that have role="button" (if any added by theme extenders).
	 */
	function initKeyboardRoleButtons() {
		document.querySelectorAll( '[role="button"]' ).forEach( function ( el ) {
			if ( el.tagName === 'BUTTON' || el.tagName === 'A' ) {
				return; // Native elements handle keyboard natively.
			}
			el.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Enter' || e.key === ' ' ) {
					e.preventDefault();
					el.click();
				}
			} );
		} );
	}

	/**
	 * Smooth scroll polyfill for skip link (if browser lacks smooth scroll support).
	 * Respects prefers-reduced-motion.
	 */
	function initSkipLink() {
		var skipLink = document.querySelector( '.skip-link' );
		if ( ! skipLink ) {
			return;
		}
		skipLink.addEventListener( 'click', function ( e ) {
			var target = document.querySelector( skipLink.getAttribute( 'href' ) );
			if ( ! target ) {
				return;
			}
			// Move focus to main content area.
			target.setAttribute( 'tabindex', '-1' );
			target.focus();
		} );
	}

	/**
	 * Mark external links with accessible indication.
	 * Adds screen-reader text "(opens in new tab)" when target="_blank".
	 */
	function annotateExternalLinks() {
		var currentHost = window.location.hostname;
		document.querySelectorAll( 'a[target="_blank"]' ).forEach( function ( link ) {
			// Only annotate once.
			if ( link.querySelector( '.external-link-notice' ) ) {
				return;
			}
			var span = document.createElement( 'span' );
			span.className = 'screen-reader-text external-link-notice';
			span.textContent = ' (opens in new tab)';
			link.appendChild( span );
			// Ensure rel includes noopener for security.
			var rel = link.getAttribute( 'rel' ) || '';
			if ( rel.indexOf( 'noopener' ) === -1 ) {
				link.setAttribute( 'rel', ( rel + ' noopener noreferrer' ).trim() );
			}
		} );
	}

	// Init on DOM ready.
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			initKeyboardRoleButtons();
			initSkipLink();
			annotateExternalLinks();
		} );
	} else {
		initKeyboardRoleButtons();
		initSkipLink();
		annotateExternalLinks();
	}

})();
