<?php
/**
 * Pro Mode: Chapter CPT, Book Taxonomy, Term Meta, and Rewrite Flushing.
 *
 * All CPT/taxonomy registration is guarded by bookwp_is_pro_mode() so that
 * Default Mode installs are completely unaffected.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ============================================================
// 1. PRO MODE: CPT + TAXONOMY
// ============================================================

/**
 * Whether Pro Mode is active.
 *
 * @return bool
 */
function bookwp_is_pro_mode() {
	return (bool) bookwp_get_option( 'enable_pro_mode' );
}

/**
 * Register the 'chapter' CPT and 'book' taxonomy when Pro Mode is active.
 * Hooked to 'init' so rewrites are registered on every request.
 */
function bookwp_register_pro_mode() {
	if ( ! bookwp_is_pro_mode() ) {
		return;
	}

	register_post_type(
		'chapter',
		array(
			'labels'       => array(
				'name'               => esc_html_x( 'Chapters', 'post type general name', 'book-wp' ),
				'singular_name'      => esc_html_x( 'Chapter', 'post type singular name', 'book-wp' ),
				'add_new'            => esc_html__( 'Add New Chapter', 'book-wp' ),
				'add_new_item'       => esc_html__( 'Add New Chapter', 'book-wp' ),
				'edit_item'          => esc_html__( 'Edit Chapter', 'book-wp' ),
				'new_item'           => esc_html__( 'New Chapter', 'book-wp' ),
				'view_item'          => esc_html__( 'View Chapter', 'book-wp' ),
				'search_items'       => esc_html__( 'Search Chapters', 'book-wp' ),
				'not_found'          => esc_html__( 'No chapters found.', 'book-wp' ),
				'not_found_in_trash' => esc_html__( 'No chapters found in Trash.', 'book-wp' ),
				'all_items'          => esc_html__( 'All Chapters', 'book-wp' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'rewrite'      => array( 'slug' => 'chapter' ),
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-book-alt',
		)
	);

	register_taxonomy(
		'book',
		'chapter',
		array(
			'labels'            => array(
				'name'          => esc_html_x( 'Books', 'taxonomy general name', 'book-wp' ),
				'singular_name' => esc_html_x( 'Book', 'taxonomy singular name', 'book-wp' ),
				'search_items'  => esc_html__( 'Search Books', 'book-wp' ),
				'all_items'     => esc_html__( 'All Books', 'book-wp' ),
				'edit_item'     => esc_html__( 'Edit Book', 'book-wp' ),
				'update_item'   => esc_html__( 'Update Book', 'book-wp' ),
				'add_new_item'  => esc_html__( 'Add New Book', 'book-wp' ),
				'new_item_name' => esc_html__( 'New Book Name', 'book-wp' ),
				'menu_name'     => esc_html__( 'Books', 'book-wp' ),
			),
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'book' ),
			'show_admin_column' => true,
		)
	);
}
add_action( 'init', 'bookwp_register_pro_mode' );

// ============================================================
// 2. REWRITE FLUSHING
// ============================================================

/**
 * Flush rewrite rules once on theme activation.
 * Registers the CPT/taxonomy first so rules include them.
 */
function bookwp_flush_rewrites_on_switch() {
	if ( bookwp_is_pro_mode() ) {
		bookwp_register_pro_mode();
	}
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'bookwp_flush_rewrites_on_switch' );

/**
 * When the Pro Mode toggle changes (from the Theme Options page or the
 * Customizer), schedule a rewrite flush. The flush itself runs on the next
 * request after the CPT has been (un)registered on init, so the rules are
 * generated against the correct post types.
 *
 * @param mixed $old_value Previous options array.
 * @param mixed $new_value New options array.
 */
function bookwp_schedule_rewrite_flush( $old_value, $new_value ) {
	$old = is_array( $old_value ) && isset( $old_value['enable_pro_mode'] ) ? (int) $old_value['enable_pro_mode'] : 0;
	$new = is_array( $new_value ) && isset( $new_value['enable_pro_mode'] ) ? (int) $new_value['enable_pro_mode'] : 0;
	if ( $old !== $new ) {
		update_option( 'bookwp_flush_rewrites', 1, false );
	}
}
add_action( 'update_option_' . BOOKWP_OPTION_NAME, 'bookwp_schedule_rewrite_flush', 10, 2 );
add_action(
	'add_option_' . BOOKWP_OPTION_NAME,
	function ( $option, $value ) {
		bookwp_schedule_rewrite_flush( array(), $value );
	},
	10,
	2
);

/**
 * Perform a scheduled rewrite flush after post types are registered.
 */
function bookwp_maybe_flush_rewrites() {
	if ( get_option( 'bookwp_flush_rewrites' ) ) {
		delete_option( 'bookwp_flush_rewrites' );
		flush_rewrite_rules();
	}
}
add_action( 'init', 'bookwp_maybe_flush_rewrites', 99 );

// ============================================================
// 3. BOOK TERM META (Pro Mode)
// ============================================================

/**
 * Register meta fields for the 'book' taxonomy term.
 * Only registered when Pro Mode is active.
 */
function bookwp_register_book_term_meta() {
	if ( ! bookwp_is_pro_mode() ) {
		return;
	}

	register_term_meta(
		'book',
		'book_cover',
		array(
			'type'              => 'integer',
			'single'            => true,
			'sanitize_callback' => 'absint',
			'show_in_rest'      => true,
		)
	);

	register_term_meta(
		'book',
		'book_description',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'wp_kses_post',
			'show_in_rest'      => true,
		)
	);

	register_term_meta(
		'book',
		'publication_year',
		array(
			'type'              => 'integer',
			'single'            => true,
			'sanitize_callback' => 'absint',
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'bookwp_register_book_term_meta' );
