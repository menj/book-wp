<?php
/**
 * Customizer Settings, Option Helper, Body Classes, and Font Map.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ============================================================
// 4. CUSTOMIZER SETTINGS
// ============================================================

/**
 * Register Customizer settings, sections, and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function bookwp_customize_register( $wp_customize ) {

	// ── Section ──────────────────────────────────────────────
	$wp_customize->add_section(
		'bookwp_book_settings',
		array(
			'title'       => esc_html__( 'Book Settings', 'book-wp' ),
			'description' => esc_html__( 'Configure the appearance and behavior of your online book.', 'book-wp' ),
			'priority'    => 30,
		)
	);

	// ── Font ─────────────────────────────────────────────────
	$wp_customize->add_setting(
		'bookwp_font',
		array(
			'default'           => 'georgia',
			'sanitize_callback' => 'bookwp_sanitize_font',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'bookwp_font',
		array(
			'label'       => esc_html__( 'Book Font', 'book-wp' ),
			'description' => esc_html__( 'Choose the typeface used for reading. Georgia is a standard web-safe font; others are loaded from Google Fonts (requires internet connection).', 'book-wp' ),
			'section'     => 'bookwp_book_settings',
			'type'        => 'select',
			'choices'     => array(
				'georgia'   => esc_html__( 'Georgia (built-in)', 'book-wp' ),
				'vollkorn'  => esc_html__( 'Vollkorn', 'book-wp' ),
				'arvo'      => esc_html__( 'Arvo', 'book-wp' ),
				'maven-pro' => esc_html__( 'Maven Pro', 'book-wp' ),
				'ubuntu'    => esc_html__( 'Ubuntu', 'book-wp' ),
			),
		)
	);

	// ── Color Scheme ─────────────────────────────────────────
	$wp_customize->add_setting(
		'bookwp_color_scheme',
		array(
			'default'           => 'black-white',
			'sanitize_callback' => 'bookwp_sanitize_color_scheme',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'bookwp_color_scheme',
		array(
			'label'       => esc_html__( 'Color Scheme', 'book-wp' ),
			'description' => esc_html__( 'All color schemes meet WCAG 2.2 AA contrast requirements.', 'book-wp' ),
			'section'     => 'bookwp_book_settings',
			'type'        => 'select',
			'choices'     => array(
				'black-white' => esc_html__( 'Black and White', 'book-wp' ),
				'cream-brown' => esc_html__( 'Cream and Brown', 'book-wp' ),
			),
		)
	);

	// ── Show Comments ────────────────────────────────────────
	$wp_customize->add_setting(
		'bookwp_show_comments',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'bookwp_show_comments',
		array(
			'label'       => esc_html__( 'Show Comments on Chapters', 'book-wp' ),
			'description' => esc_html__( 'Enable to display the comment section below each chapter.', 'book-wp' ),
			'section'     => 'bookwp_book_settings',
			'type'        => 'checkbox',
		)
	);

	// ── Front Page Blurb ─────────────────────────────────────
	$wp_customize->add_setting(
		'bookwp_front_blurb',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'bookwp_front_blurb',
		array(
			'label'       => esc_html__( 'Front Page Blurb', 'book-wp' ),
			'description' => esc_html__( 'Optional text shown above the Table of Contents on the front page. HTML is allowed.', 'book-wp' ),
			'section'     => 'bookwp_book_settings',
			'type'        => 'textarea',
		)
	);

	// ── Chapter Blurb ────────────────────────────────────────
	$wp_customize->add_setting(
		'bookwp_chapter_blurb',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'bookwp_chapter_blurb',
		array(
			'label'       => esc_html__( 'Chapter Header Blurb', 'book-wp' ),
			'description' => esc_html__( 'Optional text shown in the site header on chapter pages (e.g. series name or tagline). HTML is allowed.', 'book-wp' ),
			'section'     => 'bookwp_book_settings',
			'type'        => 'textarea',
		)
	);

	// ── Copyright ────────────────────────────────────────────
	$wp_customize->add_setting(
		'bookwp_copyright',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'bookwp_copyright',
		array(
			'label'       => esc_html__( 'Copyright Notice', 'book-wp' ),
			'description' => esc_html__( 'Displayed in the footer. Plain text only. To include a copyright symbol type: (c)', 'book-wp' ),
			'section'     => 'bookwp_book_settings',
			'type'        => 'text',
		)
	);

	// ── Pro Mode ─────────────────────────────────────────────
	$wp_customize->add_setting(
		'bookwp_enable_pro_mode',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'bookwp_enable_pro_mode',
		array(
			'label'       => esc_html__( 'Enable Pro Mode (Custom Post Types)', 'book-wp' ),
			'description' => esc_html__( 'When enabled, chapters become a Custom Post Type grouped under a "Book" taxonomy. After toggling, visit Settings → Permalinks to flush rewrite rules.', 'book-wp' ),
			'section'     => 'bookwp_book_settings',
			'type'        => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'bookwp_customize_register' );

// ── Sanitize Callbacks ───────────────────────────────────────

/**
 * Sanitize font choice.
 *
 * @param string $value Raw value.
 * @return string Sanitized value.
 */
function bookwp_sanitize_font( $value ) {
	$valid = array( 'georgia', 'vollkorn', 'arvo', 'maven-pro', 'ubuntu' );
	return in_array( $value, $valid, true ) ? $value : 'georgia';
}

/**
 * Sanitize color scheme choice.
 *
 * @param string $value Raw value.
 * @return string Sanitized value.
 */
function bookwp_sanitize_color_scheme( $value ) {
	$valid = array( 'black-white', 'cream-brown' );
	return in_array( $value, $valid, true ) ? $value : 'black-white';
}

// ============================================================
// 5. OPTION HELPER (with legacy fallback)
// ============================================================

/**
 * Get a theme option from the Customizer, with automatic fallback to legacy
 * book_wp_* option keys so existing installs don't lose their settings.
 *
 * @param string $key     Customizer setting ID (e.g. 'bookwp_font').
 * @param mixed  $default Default value if nothing is found.
 * @return mixed Option value.
 */
function bookwp_get_option( $key, $default = '' ) {
	$value = get_theme_mod( $key, null );
	if ( null !== $value && '' !== $value ) {
		return $value;
	}

	// Map new keys to legacy book_wp_* option names.
	$legacy_map = array(
		'bookwp_font'            => 'book_wp_font',
		'bookwp_color_scheme'    => 'book_wp_color',
		'bookwp_show_comments'   => 'book_wp_comments',
		'bookwp_front_blurb'     => 'book_wp_front_blurb',
		'bookwp_chapter_blurb'   => 'book_wp_chapter_blurb',
		'bookwp_copyright'       => 'book_wp_copyright',
	);

	if ( isset( $legacy_map[ $key ] ) ) {
		$legacy = get_option( $legacy_map[ $key ], null );
		if ( null !== $legacy && '' !== $legacy ) {
			if ( 'bookwp_color_scheme' === $key ) {
				return ( 'c_n_b' === $legacy ) ? 'cream-brown' : 'black-white';
			}
			if ( 'bookwp_show_comments' === $key ) {
				return ( 'show' === $legacy ) ? 1 : 0;
			}
			return $legacy;
		}
	}

	return $default;
}

// ============================================================
// 6. BODY CLASS (font + color scheme)
// ============================================================

/**
 * Add font and color-scheme CSS classes to <body>.
 *
 * @param string[] $classes Existing body classes.
 * @return string[] Modified body classes.
 */
function bookwp_body_classes( $classes ) {
	$font   = bookwp_get_option( 'bookwp_font', 'georgia' );
	$scheme = bookwp_get_option( 'bookwp_color_scheme', 'black-white' );

	if ( 'georgia' !== $font ) {
		$classes[] = 'font-' . sanitize_html_class( $font );
	}

	if ( 'cream-brown' === $scheme ) {
		$classes[] = 'color-scheme-cream-brown';
	}

	return $classes;
}
add_filter( 'body_class', 'bookwp_body_classes' );

// ============================================================
// 7. FONT MAP
// ============================================================

/**
 * Returns the supported Google Font definitions (HTTPS only).
 *
 * @return array<string, array{url: string, family: string}>
 */
function bookwp_get_font_map() {
	return array(
		'vollkorn'  => array(
			'url'    => 'https://fonts.googleapis.com/css2?family=Vollkorn:ital,wght@0,400;0,700;1,400;1,700&display=swap',
			'family' => "'Vollkorn', Georgia, serif",
		),
		'arvo'      => array(
			'url'    => 'https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400;1,700&display=swap',
			'family' => "'Arvo', Georgia, serif",
		),
		'maven-pro' => array(
			'url'    => 'https://fonts.googleapis.com/css2?family=Maven+Pro:wght@400;500;700;900&display=swap',
			'family' => "'Maven Pro', sans-serif",
		),
		'ubuntu'    => array(
			'url'    => 'https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,400;0,700;1,400;1,700&display=swap',
			'family' => "'Ubuntu', sans-serif",
		),
	);
}
