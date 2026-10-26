/**
 * Book WP — Theme Options admin script
 * Tabs, colour pickers, and an unsaved-changes hint.
 * Depends on wp-color-picker (jQuery is only used through that API).
 */

( function () {
	'use strict';

	var config = window.bookwpAdmin || { tabs: [], defaultTab: 'design', i18n: {} };

	/**
	 * Tabbed interface. Progressive enhancement over plain links: without JS the
	 * links reload the page with ?tab=, and the active panel is rendered server-side.
	 */
	function initTabs() {
		var tabs = Array.prototype.slice.call( document.querySelectorAll( '.bookwp-tabs__tab' ) );
		var panels = Array.prototype.slice.call( document.querySelectorAll( '.bookwp-panel' ) );
		var activeInput = document.getElementById( 'bookwp-active-tab' );

		if ( ! tabs.length || ! panels.length ) {
			return;
		}

		function activate( slug, focusTab ) {
			tabs.forEach( function ( tab ) {
				var isActive = tab.getAttribute( 'data-tab' ) === slug;
				tab.classList.toggle( 'is-active', isActive );
				tab.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
				tab.setAttribute( 'tabindex', isActive ? '0' : '-1' );
				if ( isActive && focusTab ) {
					tab.focus();
				}
			} );
			panels.forEach( function ( panel ) {
				var isActive = panel.id === 'bookwp-panel-' + slug;
				panel.classList.toggle( 'is-active', isActive );
				if ( isActive ) {
					panel.removeAttribute( 'hidden' );
				} else {
					panel.setAttribute( 'hidden', '' );
				}
			} );
			if ( activeInput ) {
				activeInput.value = slug;
			}
			if ( window.history && window.history.replaceState ) {
				var url = new URL( window.location.href );
				url.searchParams.set( 'tab', slug );
				window.history.replaceState( null, '', url.toString() );
			}
		}

		tabs.forEach( function ( tab, index ) {
			tab.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				activate( tab.getAttribute( 'data-tab' ), false );
			} );

			// Arrow-key navigation per WAI-ARIA tabs pattern.
			tab.addEventListener( 'keydown', function ( e ) {
				var next;
				if ( e.key === 'ArrowDown' || e.key === 'ArrowRight' ) {
					next = tabs[ ( index + 1 ) % tabs.length ];
				} else if ( e.key === 'ArrowUp' || e.key === 'ArrowLeft' ) {
					next = tabs[ ( index - 1 + tabs.length ) % tabs.length ];
				} else if ( e.key === 'Home' ) {
					next = tabs[ 0 ];
				} else if ( e.key === 'End' ) {
					next = tabs[ tabs.length - 1 ];
				}
				if ( next ) {
					e.preventDefault();
					activate( next.getAttribute( 'data-tab' ), true );
				}
			} );
		} );
	}

	/**
	 * WordPress colour pickers on custom-colour fields.
	 * Empty value means "use the scheme colour"; the Clear button restores that.
	 */
	function initColorPickers() {
		if ( ! window.jQuery || ! window.jQuery.fn.wpColorPicker ) {
			return;
		}
		window.jQuery( '.bookwp-color-field' ).each( function () {
			var $field = window.jQuery( this );
			$field.wpColorPicker( {
				defaultColor: false,
				hide: true,
				clear: function () {
					$field.val( '' ).trigger( 'change' );
				}
			} );
		} );
	}

	/**
	 * Mirror the :has() styling for browsers that lack it.
	 */
	function initRadioFallback() {
		var supportsHas = false;
		try {
			supportsHas = window.CSS && window.CSS.supports && window.CSS.supports( 'selector(:has(*))' );
		} catch ( err ) {
			supportsHas = false;
		}
		if ( supportsHas ) {
			return;
		}
		document.querySelectorAll( '.bookwp-radio-group' ).forEach( function ( group ) {
			var labels = group.querySelectorAll( '.bookwp-radio' );
			function sync() {
				labels.forEach( function ( label ) {
					var input = label.querySelector( 'input' );
					label.classList.toggle( 'is-checked', !! ( input && input.checked ) );
				} );
			}
			group.addEventListener( 'change', sync );
			labels.forEach( function ( label ) {
				var input = label.querySelector( 'input' );
				if ( ! input ) {
					return;
				}
				input.addEventListener( 'focus', function () {
					label.classList.add( 'is-focused' );
				} );
				input.addEventListener( 'blur', function () {
					label.classList.remove( 'is-focused' );
				} );
			} );
			sync();
		} );
	}

	/**
	 * Show an unsaved-changes hint and warn before leaving the page.
	 */
	function initDirtyState() {
		var form = document.getElementById( 'bookwp-options-form' );
		var hint = document.getElementById( 'bookwp-dirty' );
		if ( ! form ) {
			return;
		}
		var dirty = false;

		function markDirty() {
			if ( dirty ) {
				return;
			}
			dirty = true;
			if ( hint ) {
				hint.textContent = config.i18n.unsaved || '';
			}
		}

		form.addEventListener( 'change', markDirty );
		form.addEventListener( 'input', markDirty );
		form.addEventListener( 'submit', function () {
			dirty = false;
		} );
		window.addEventListener( 'beforeunload', function ( e ) {
			if ( dirty ) {
				e.preventDefault();
				e.returnValue = '';
			}
		} );
	}

	function init() {
		initTabs();
		initColorPickers();
		initRadioFallback();
		initDirtyState();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
