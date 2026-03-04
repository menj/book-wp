<?php
/**
 * Structured Data, Extensibility Hooks, and Head Cleanup.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ============================================================
// 13. JSON-LD STRUCTURED DATA (Minimal, filterable)
// ============================================================

/**
 * Output minimal Article JSON-LD on chapter/post singles.
 *
 * Skipped automatically when a major SEO plugin is active.
 * Disable entirely via: add_filter('bookwp_enable_structured_data', '__return_false');
 */
function bookwp_structured_data() {
	if ( ! apply_filters( 'bookwp_enable_structured_data', true ) ) {
		return;
	}

	// Defer to any active SEO plugin.
	if (
		defined( 'WPSEO_VERSION' )    ||  // Yoast SEO.
		defined( 'AIOSEO_VERSION' )   ||  // All in One SEO.
		class_exists( 'RankMath' )    ||  // Rank Math.
		defined( 'SLP_VERSION' )          // SEOPress.
	) {
		return;
	}

	$post_type = bookwp_is_pro_mode() ? 'chapter' : 'post';
	if ( ! is_singular( $post_type ) ) {
		return;
	}

	$post = get_post();
	if ( ! $post ) {
		return;
	}

	$data = array(
		'@context'      => 'https://schema.org',
		'@type'         => 'Article',
		'headline'      => esc_html( get_the_title( $post ) ),
		'datePublished' => esc_html( get_the_date( 'c', $post ) ),
		'dateModified'  => esc_html( get_the_modified_date( 'c', $post ) ),
		'inLanguage'    => esc_html( get_bloginfo( 'language' ) ),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $data ) . '</script>' . "\n";
}
add_action( 'wp_head', 'bookwp_structured_data' );

// ============================================================
// 14. EXTENSIBILITY HOOKS
// ============================================================

/**
 * Fire before chapter content is output.
 * Hook: bookwp_before_chapter_content
 *
 * @param WP_Post $post The current post object.
 */
function bookwp_before_chapter_content( $post ) {
	do_action( 'bookwp_before_chapter_content', $post );
}

/**
 * Fire after chapter content is output.
 * Hook: bookwp_after_chapter_content
 *
 * @param WP_Post $post The current post object.
 */
function bookwp_after_chapter_content( $post ) {
	do_action( 'bookwp_after_chapter_content', $post );
}

// ============================================================
// 15. HEAD CLEANUP
// ============================================================

/**
 * Remove unnecessary <head> links that have no value for a book theme.
 */
function bookwp_head_cleanup() {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
}
add_action( 'init', 'bookwp_head_cleanup' ); // Function defined above — correct order.
