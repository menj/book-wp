/**
 * Book WP — Main JavaScript
 * License: GPL-2.0-or-later
 *
 * No jQuery dependency. Vanilla JS only.
 * Translatable strings arrive via wp_localize_script() as window.bookwpL10n.
 */

( function () {
	'use strict';

	var l10n = window.bookwpL10n || {};

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
	 * Move focus to the main content area when the skip link is used.
	 */
	function initSkipLink() {
		var skipLink = document.querySelector( '.skip-link' );
		if ( ! skipLink ) {
			return;
		}
		skipLink.addEventListener( 'click', function () {
			var target = document.querySelector( skipLink.getAttribute( 'href' ) );
			if ( ! target ) {
				return;
			}
			target.setAttribute( 'tabindex', '-1' );
			target.focus();
		} );
	}

	/**
	 * Mark external links with accessible indication.
	 * Adds screen-reader text when target="_blank" and ensures rel="noopener".
	 */
	function annotateExternalLinks() {
		var notice = l10n.opensInNewTab || '(opens in new tab)';
		document.querySelectorAll( 'a[target="_blank"]' ).forEach( function ( link ) {
			if ( link.querySelector( '.external-link-notice' ) ) {
				return; // Only annotate once.
			}
			var span = document.createElement( 'span' );
			span.className = 'screen-reader-text external-link-notice';
			span.textContent = ' ' + notice;
			link.appendChild( span );

			var rel = link.getAttribute( 'rel' ) || '';
			if ( rel.indexOf( 'noopener' ) === -1 ) {
				link.setAttribute( 'rel', ( rel + ' noopener noreferrer' ).trim() );
			}
		} );
	}

	function init() {
		initKeyboardRoleButtons();
		initSkipLink();
		annotateExternalLinks();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
