<?php
/**
 * Theme Setup, Asset Enqueue, and Widget Registration.
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

		// Editor styles — loads assets/css/book-wp.css into the block editor canvas.
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/book-wp.css' );

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
 * Enqueue scripts and styles.
 */
function bookwp_enqueue_assets() {
	$ver = '2.0.0';

	// Main stylesheet.
	wp_enqueue_style(
		'book-wp-style',
		get_template_directory_uri() . '/assets/css/book-wp.css',
		array(),
		$ver
	);

	// Font: enqueue Google Font based on Customizer setting (HTTPS only).
	$font     = bookwp_get_option( 'bookwp_font', 'georgia' );
	$font_map = bookwp_get_font_map();
	if ( 'georgia' !== $font && isset( $font_map[ $font ] ) ) {
		wp_enqueue_style(
			'book-wp-font-' . sanitize_key( $font ),
			esc_url( $font_map[ $font ]['url'] ),
			array(),
			null
		);
	}

	// Main JS — only loaded on singular and front-page views.
	if ( is_singular() || is_front_page() ) {
		wp_enqueue_script(
			'book-wp-main',
			get_template_directory_uri() . '/assets/js/book-wp.js',
			array(),
			$ver,
			true
		);
	}

	// Comment reply script.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bookwp_enqueue_assets' );

// ============================================================
// 3. SIDEBAR
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
