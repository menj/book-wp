<?php
/**
 * Option Schema, Option Helper, Font Map, and Colour Schemes.
 *
 * All Book WP settings are stored in a single array option, `bookwp_options`.
 * Both the Theme Options page (inc/theme-options.php) and the Customizer
 * (inc/customizer.php) read and write that same array, so the two surfaces
 * never drift apart.
 *
 * Reads fall back to the 2.0.x theme_mod keys and then to the 1.x
 * `book_wp_*` option keys, so upgraded sites keep their settings without
 * any manual migration step.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Name of the array option that holds every Book WP setting.
 */
const BOOKWP_OPTION_NAME = 'bookwp_options';

// ============================================================
// 1. SCHEMA
// ============================================================

/**
 * Return the option schema: every field, its default, sanitizer, and tab.
 *
 * The schema drives the Theme Options page, the Customizer, and sanitization,
 * so adding a setting means adding one entry here plus a control renderer.
 *
 * @return array<string, array<string, mixed>>
 */
function bookwp_get_option_schema() {
	static $schema = null;
	if ( null !== $schema ) {
		return $schema;
	}

	$schema = array(
		// ── Typography & Colours ─────────────────────────────
		'font'            => array(
			'tab'         => 'design',
			'type'        => 'select',
			'default'     => 'georgia',
			'sanitize'    => 'bookwp_sanitize_font',
			'label'       => __( 'Book Font', 'book-wp' ),
			'description' => __( 'Typeface used for reading. Georgia is a web-safe font that needs no download; the others are loaded from Google Fonts.', 'book-wp' ),
			'choices'     => 'bookwp_get_font_choices',
		),
		'color_scheme'    => array(
			'tab'         => 'design',
			'type'        => 'radio',
			'default'     => 'black-white',
			'sanitize'    => 'bookwp_sanitize_color_scheme',
			'label'       => __( 'Colour Scheme', 'book-wp' ),
			'description' => __( 'Every scheme meets WCAG 2.2 AA contrast. "Auto" follows the reader\'s operating-system light or dark preference.', 'book-wp' ),
			'choices'     => 'bookwp_get_color_scheme_choices',
		),
		'color_bg'        => array(
			'tab'         => 'design',
			'type'        => 'color',
			'default'     => '',
			'sanitize'    => 'bookwp_sanitize_hex_color',
			'label'       => __( 'Custom Background', 'book-wp' ),
			'description' => __( 'Optional. Overrides the scheme background colour.', 'book-wp' ),
		),
		'color_text'      => array(
			'tab'         => 'design',
			'type'        => 'color',
			'default'     => '',
			'sanitize'    => 'bookwp_sanitize_hex_color',
			'label'       => __( 'Custom Text Colour', 'book-wp' ),
			'description' => __( 'Optional. Overrides the scheme body text and heading colour.', 'book-wp' ),
		),
		'color_accent'    => array(
			'tab'         => 'design',
			'type'        => 'color',
			'default'     => '',
			'sanitize'    => 'bookwp_sanitize_hex_color',
			'label'       => __( 'Custom Accent Colour', 'book-wp' ),
			'description' => __( 'Optional. Overrides links, focus rings, and the current-chapter marker. Check contrast against your background.', 'book-wp' ),
		),

		// ── Content & Blurbs ─────────────────────────────────
		'front_blurb'     => array(
			'tab'         => 'content',
			'type'        => 'textarea',
			'default'     => '',
			'sanitize'    => 'wp_kses_post',
			'label'       => __( 'Front Page Blurb', 'book-wp' ),
			'description' => __( 'Optional text shown above the Table of Contents on the front page. Basic HTML is allowed.', 'book-wp' ),
		),
		'chapter_blurb'   => array(
			'tab'         => 'content',
			'type'        => 'textarea',
			'default'     => '',
			'sanitize'    => 'wp_kses_post',
			'label'       => __( 'Chapter Header Blurb', 'book-wp' ),
			'description' => __( 'Optional text shown in the site header on chapter pages, such as a series name or tagline. Basic HTML is allowed.', 'book-wp' ),
		),
		'copyright'       => array(
			'tab'         => 'content',
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'label'       => __( 'Copyright Notice', 'book-wp' ),
			'description' => __( 'Displayed in the footer. Plain text only. Type (c) for the copyright symbol.', 'book-wp' ),
		),

		// ── Advanced ─────────────────────────────────────────
		'show_comments'   => array(
			'tab'         => 'advanced',
			'type'        => 'checkbox',
			'default'     => 0,
			'sanitize'    => 'bookwp_sanitize_checkbox',
			'label'       => __( 'Show Comments on Chapters', 'book-wp' ),
			'description' => __( 'Display the comment section below each chapter when comments are open.', 'book-wp' ),
		),
		'enable_pro_mode' => array(
			'tab'         => 'advanced',
			'type'        => 'checkbox',
			'default'     => 0,
			'sanitize'    => 'bookwp_sanitize_checkbox',
			'label'       => __( 'Enable Pro Mode (Custom Post Types)', 'book-wp' ),
			'description' => __( 'Chapters become a dedicated post type grouped under a "Book" taxonomy. Permalinks are refreshed automatically when this changes.', 'book-wp' ),
		),
	);

	return $schema;
}

/**
 * Return the tabs for the Theme Options page and the matching Customizer sections.
 *
 * @return array<string, array{title: string, description: string}>
 */
function bookwp_get_option_tabs() {
	return array(
		'design'   => array(
			'title'       => __( 'Typography & Colours', 'book-wp' ),
			'description' => __( 'Choose the reading typeface and colour scheme, or set your own colours.', 'book-wp' ),
		),
		'content'  => array(
			'title'       => __( 'Content & Blurbs', 'book-wp' ),
			'description' => __( 'Optional text shown on the front page, in the chapter header, and in the footer.', 'book-wp' ),
		),
		'advanced' => array(
			'title'       => __( 'Advanced', 'book-wp' ),
			'description' => __( 'Comments and the optional Pro Mode content model.', 'book-wp' ),
		),
	);
}

/**
 * Return the default value for every option.
 *
 * @return array<string, mixed>
 */
function bookwp_get_option_defaults() {
	return wp_list_pluck( bookwp_get_option_schema(), 'default' );
}

// ============================================================
// 2. OPTION HELPER (with 2.0 theme_mod and 1.x legacy fallback)
// ============================================================

/**
 * Normalise a key: accepts both `font` and the 2.0-era `bookwp_font`.
 *
 * @param string $key Option key.
 * @return string
 */
function bookwp_normalize_option_key( $key ) {
	return ( 0 === strpos( $key, 'bookwp_' ) ) ? substr( $key, 7 ) : $key;
}

/**
 * Return every option, merged over defaults and with legacy fallbacks applied.
 *
 * @param bool $reset Internal. True clears the per-request cache and returns an empty array.
 * @return array<string, mixed>
 */
function bookwp_get_options( $reset = false ) {
	static $cache = null;
	if ( $reset ) {
		$cache = null;
		return array();
	}
	if ( null !== $cache ) {
		return $cache;
	}

	$defaults = bookwp_get_option_defaults();
	$stored   = get_option( BOOKWP_OPTION_NAME, null );
	$options  = is_array( $stored ) ? array_merge( $defaults, $stored ) : $defaults;

	// Only fall back to older storage when nothing has been saved under the new key.
	if ( ! is_array( $stored ) ) {
		foreach ( $defaults as $key => $default ) {
			$legacy = bookwp_get_legacy_option( $key );
			if ( null !== $legacy ) {
				$options[ $key ] = $legacy;
			}
		}
	}

	// Always run sanitizers so unexpected stored values cannot reach templates.
	foreach ( bookwp_get_option_schema() as $key => $field ) {
		if ( isset( $options[ $key ] ) && is_callable( $field['sanitize'] ) ) {
			$options[ $key ] = call_user_func( $field['sanitize'], $options[ $key ] );
		}
	}

	$cache = $options;
	return $cache;
}

/**
 * Clear the in-request options cache. Hooked to option updates so a save and a
 * subsequent read in the same request stay consistent.
 */
function bookwp_flush_options_cache() {
	bookwp_get_options( true );
}
add_action( 'update_option_' . BOOKWP_OPTION_NAME, 'bookwp_flush_options_cache', 1 );
add_action( 'add_option_' . BOOKWP_OPTION_NAME, 'bookwp_flush_options_cache', 1 );
add_action( 'delete_option_' . BOOKWP_OPTION_NAME, 'bookwp_flush_options_cache', 1 );

/**
 * Read a single theme option.
 *
 * @param string $key     Option key, with or without the `bookwp_` prefix.
 * @param mixed  $default Default when the key is unknown.
 * @return mixed
 */
function bookwp_get_option( $key, $default = '' ) {
	$key     = bookwp_normalize_option_key( $key );
	$options = bookwp_get_options();
	return array_key_exists( $key, $options ) ? $options[ $key ] : $default;
}

/**
 * Look up a value from 2.0.x theme_mods or 1.x `book_wp_*` options.
 *
 * @param string $key Normalised option key.
 * @return mixed|null Null when nothing legacy exists.
 */
function bookwp_get_legacy_option( $key ) {
	// 2.0.x stored each setting as a theme_mod named bookwp_<key>.
	$mod = get_theme_mod( 'bookwp_' . $key, null );
	if ( null !== $mod && '' !== $mod ) {
		return $mod;
	}

	// 1.x stored settings as individual book_wp_* options.
	$legacy_map = array(
		'font'          => 'book_wp_font',
		'color_scheme'  => 'book_wp_color',
		'show_comments' => 'book_wp_comments',
		'front_blurb'   => 'book_wp_front_blurb',
		'chapter_blurb' => 'book_wp_chapter_blurb',
		'copyright'     => 'book_wp_copyright',
	);
	if ( ! isset( $legacy_map[ $key ] ) ) {
		return null;
	}

	$legacy = get_option( $legacy_map[ $key ], null );
	if ( null === $legacy || '' === $legacy ) {
		return null;
	}

	switch ( $key ) {
		case 'font':
			// 1.x used "maven" for Maven Pro.
			return ( 'maven' === $legacy ) ? 'maven-pro' : $legacy;
		case 'color_scheme':
			return ( 'c_n_b' === $legacy ) ? 'cream-brown' : 'black-white';
		case 'show_comments':
			return ( 'show' === $legacy ) ? 1 : 0;
	}
	return $legacy;
}

/**
 * Sanitize the whole options array. Registered as the Settings API callback
 * and reused by the Customizer per-field sanitizers.
 *
 * @param mixed $input Raw input.
 * @return array<string, mixed>
 */
function bookwp_sanitize_options( $input ) {
	$input  = is_array( $input ) ? $input : array();
	$output = array();

	foreach ( bookwp_get_option_schema() as $key => $field ) {
		$raw = array_key_exists( $key, $input ) ? $input[ $key ] : ( 'checkbox' === $field['type'] ? 0 : $field['default'] );
		$output[ $key ] = is_callable( $field['sanitize'] ) ? call_user_func( $field['sanitize'], $raw ) : $field['default'];
	}

	return $output;
}

// ============================================================
// 3. SANITIZERS
// ============================================================

/**
 * Sanitize font choice.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function bookwp_sanitize_font( $value ) {
	$value = is_string( $value ) ? $value : '';
	if ( 'maven' === $value ) {
		$value = 'maven-pro';
	}
	return array_key_exists( $value, bookwp_get_font_choices() ) ? $value : 'georgia';
}

/**
 * Sanitize colour scheme choice.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function bookwp_sanitize_color_scheme( $value ) {
	$value = is_string( $value ) ? $value : '';
	return array_key_exists( $value, bookwp_get_color_scheme_choices() ) ? $value : 'black-white';
}

/**
 * Sanitize an optional hex colour. Empty string means "use the scheme colour".
 *
 * @param mixed $value Raw value.
 * @return string
 */
function bookwp_sanitize_hex_color( $value ) {
	if ( ! is_string( $value ) || '' === trim( $value ) ) {
		return '';
	}
	$hex = sanitize_hex_color( trim( $value ) );
	return $hex ? $hex : '';
}

/**
 * Sanitize a checkbox to 0 or 1.
 *
 * @param mixed $value Raw value.
 * @return int
 */
function bookwp_sanitize_checkbox( $value ) {
	return ( 1 === $value || '1' === $value || true === $value || 'on' === $value ) ? 1 : 0;
}

// ============================================================
// 4. FONT MAP
// ============================================================

/**
 * Returns the supported Google Font definitions (HTTPS only).
 *
 * To add a font, add an entry here and a matching `.font-<slug>` rule in
 * assets/css/book-wp.css plus a fontFamilies entry in theme.json.
 *
 * @return array<string, array{label: string, url: string, family: string}>
 */
function bookwp_get_font_map() {
	$fonts = array(
		'vollkorn'  => array(
			'label'  => __( 'Vollkorn', 'book-wp' ),
			'url'    => 'https://fonts.googleapis.com/css2?family=Vollkorn:ital,wght@0,400;0,700;1,400;1,700&display=swap',
			'family' => "'Vollkorn', Georgia, serif",
		),
		'arvo'      => array(
			'label'  => __( 'Arvo', 'book-wp' ),
			'url'    => 'https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400;1,700&display=swap',
			'family' => "'Arvo', Georgia, serif",
		),
		'maven-pro' => array(
			'label'  => __( 'Maven Pro', 'book-wp' ),
			'url'    => 'https://fonts.googleapis.com/css2?family=Maven+Pro:wght@400;500;700;900&display=swap',
			'family' => "'Maven Pro', sans-serif",
		),
		'ubuntu'    => array(
			'label'  => __( 'Ubuntu', 'book-wp' ),
			'url'    => 'https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,400;0,700;1,400;1,700&display=swap',
			'family' => "'Ubuntu', sans-serif",
		),
	);

	/**
	 * Filter the Google Font map. Each entry needs label, url, and family.
	 *
	 * @param array $fonts Font definitions keyed by slug.
	 */
	return apply_filters( 'bookwp_font_map', $fonts );
}

/**
 * Return font choices (slug => label) for select controls.
 *
 * @return array<string, string>
 */
function bookwp_get_font_choices() {
	$choices = array( 'georgia' => __( 'Georgia (built-in, no download)', 'book-wp' ) );
	foreach ( bookwp_get_font_map() as $slug => $font ) {
		$choices[ $slug ] = $font['label'];
	}
	return $choices;
}

// ============================================================
// 5. COLOUR SCHEMES
// ============================================================

/**
 * Return colour scheme choices (slug => label).
 *
 * The CSS for each scheme lives in assets/css/book-wp.css under a
 * `.color-scheme-<slug>` body class; `auto` switches between the default
 * and dark palettes via prefers-color-scheme.
 *
 * @return array<string, string>
 */
function bookwp_get_color_scheme_choices() {
	return apply_filters(
		'bookwp_color_scheme_choices',
		array(
			'black-white' => __( 'Black and White', 'book-wp' ),
			'cream-brown' => __( 'Cream and Brown', 'book-wp' ),
			'dark'        => __( 'Dark (night reading)', 'book-wp' ),
			'auto'        => __( 'Auto (follow system light/dark)', 'book-wp' ),
		)
	);
}

/**
 * Build the inline CSS for user-chosen custom colours, if any.
 *
 * Custom colours override the active scheme's custom properties on :root,
 * so every component picks them up without extra selectors.
 *
 * @return string Empty when no custom colour is set.
 */
function bookwp_get_custom_color_css() {
	$bg     = bookwp_get_option( 'color_bg' );
	$text   = bookwp_get_option( 'color_text' );
	$accent = bookwp_get_option( 'color_accent' );

	if ( ! $bg && ! $text && ! $accent ) {
		return '';
	}

	$props = array();
	if ( $bg ) {
		$props[] = '--color-bg:' . $bg;
	}
	if ( $text ) {
		$props[] = '--color-text:' . $text;
		$props[] = '--color-heading:' . $text;
	}
	if ( $accent ) {
		$props[] = '--color-link:' . $accent;
		$props[] = '--color-link-hover:' . $accent;
		$props[] = '--color-focus:' . $accent;
		$props[] = '--color-accent:' . $accent;
		$props[] = '--color-toc-current:' . $accent;
	}

	// Specificity: .bookwp-custom-colors on <body> beats any scheme class and the auto media query.
	return 'body.bookwp-custom-colors{' . implode( ';', $props ) . '}';
}

// ============================================================
// 6. ONE-TIME MIGRATION
// ============================================================

/**
 * Seed `bookwp_options` from 2.0.x theme_mods / 1.x options the first time an
 * administrator visits the dashboard, so partial saves from the Customizer
 * can never drop legacy values that were still being read through fallbacks.
 */
function bookwp_maybe_migrate_options() {
	if ( false !== get_option( BOOKWP_OPTION_NAME, false ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	add_option( BOOKWP_OPTION_NAME, bookwp_sanitize_options( bookwp_get_options() ), '', false );
}
add_action( 'admin_init', 'bookwp_maybe_migrate_options' );
