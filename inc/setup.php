<?php
/**
 * Theme Setup, Asset Enqueue, Body Classes, and Widget Registration.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ============================================================
// 1. THEME SETUP
// ============================================================

if ( ! function_exists( 'bookwp_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	function bookwp_setup() {
		// Translations.
		load_theme_textdomain( 'book-wp', get_template_directory() . '/languages' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// RSS feed links in <head>.
		add_theme_support( 'automatic-feed-links' );

		// Featured images.
		add_theme_support( 'post-thumbnails' );

		// HTML5 markup — include search-form for WP-generated accessible output.
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
				'search-form',
			)
		);

		// Editor styles — a dedicated sheet with typography and colour tokens only,
		// so page-chrome rules (html, #page-wrap, skip link) never leak into the editor.
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/editor.css' );

		// Opt in to WP core block default styles (normalises block output).
		add_theme_support( 'wp-block-styles' );

		// Responsive embeds.
		add_theme_support( 'responsive-embeds' );

		// Hint max content width for oEmbed / image resizing (pixels, no unit).
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 720;
		}

		// Register nav menus. Renders via wp_nav_menu() in template-parts/global/site-branding.php.
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary Navigation', 'book-wp' ),
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'bookwp_setup' );

// ============================================================
// 2. ENQUEUE ASSETS
// ============================================================

/**
 * Enqueue front-end scripts and styles.
 */
function bookwp_enqueue_assets() {
	// Main stylesheet.
	wp_enqueue_style(
		'book-wp-style',
		get_template_directory_uri() . '/assets/css/book-wp.css',
		array(),
		BOOKWP_VERSION
	);

	// Font: enqueue Google Font based on the chosen typeface (HTTPS only).
	$font     = bookwp_get_option( 'font' );
	$font_map = bookwp_get_font_map();
	if ( 'georgia' !== $font && isset( $font_map[ $font ] ) ) {
		wp_enqueue_style(
			'book-wp-font-' . sanitize_key( $font ),
			esc_url( $font_map[ $font ]['url'] ),
			array(),
			null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts URL is versioned by its query string.
		);
	}

	// Custom colour overrides chosen on the Theme Options page.
	$custom_css = bookwp_get_custom_color_css();
	if ( $custom_css ) {
		wp_add_inline_style( 'book-wp-style', $custom_css );
	}

	// Main JS — only loaded on singular and front-page views.
	if ( is_singular() || is_front_page() ) {
		wp_enqueue_script(
			'book-wp-main',
			get_template_directory_uri() . '/assets/js/book-wp.js',
			array(),
			BOOKWP_VERSION,
			true
		);
		wp_localize_script(
			'book-wp-main',
			'bookwpL10n',
			array(
				'opensInNewTab' => __( '(opens in new tab)', 'book-wp' ),
			)
		);
	}

	// Comment reply script.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bookwp_enqueue_assets' );

/**
 * Load the chosen Google Font inside the block editor too, so the editor
 * canvas matches the front end.
 */
function bookwp_enqueue_editor_font() {
	$font     = bookwp_get_option( 'font' );
	$font_map = bookwp_get_font_map();
	if ( 'georgia' !== $font && isset( $font_map[ $font ] ) ) {
		add_editor_style( esc_url_raw( $font_map[ $font ]['url'] ) );
	}
}
add_action( 'after_setup_theme', 'bookwp_enqueue_editor_font', 11 );

// ============================================================
// 3. BODY CLASSES (font + colour scheme + custom colours)
// ============================================================

/**
 * Add font and colour-scheme CSS classes to <body>.
 *
 * @param string[] $classes Existing body classes.
 * @return string[] Modified body classes.
 */
function bookwp_body_classes( $classes ) {
	$font   = bookwp_get_option( 'font' );
	$scheme = bookwp_get_option( 'color_scheme' );

	if ( 'georgia' !== $font ) {
		$classes[] = 'font-' . sanitize_html_class( $font );
	}

	if ( 'black-white' !== $scheme ) {
		$classes[] = 'color-scheme-' . sanitize_html_class( $scheme );
	}

	if ( bookwp_get_custom_color_css() ) {
		$classes[] = 'bookwp-custom-colors';
	}

	return $classes;
}
add_filter( 'body_class', 'bookwp_body_classes' );

/**
 * Tell browsers which colour schemes the page supports, so form controls and
 * scrollbars match the dark and auto schemes.
 */
function bookwp_color_scheme_meta() {
	$scheme = bookwp_get_option( 'color_scheme' );
	if ( 'dark' === $scheme ) {
		echo '<meta name="color-scheme" content="dark">' . "\n";
	} elseif ( 'auto' === $scheme ) {
		echo '<meta name="color-scheme" content="light dark">' . "\n";
	}
}
add_action( 'wp_head', 'bookwp_color_scheme_meta', 1 );

// ============================================================
// 4. SIDEBAR
// ============================================================

/**
 * Register widget area.
 */
function bookwp_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar Widgets', 'book-wp' ),
			'id'            => 'sidebar-widgets',
			'description'   => esc_html__( 'Widgets displayed in the sidebar.', 'book-wp' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'bookwp_widgets_init' );
