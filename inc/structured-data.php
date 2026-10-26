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
// JSON-LD STRUCTURED DATA
// ============================================================
//
// Follows Google's structured data guidance for the two features that apply
// to a book site: Article (chapters and blog posts) and Breadcrumb (chapters).
// Every property describes content that is visible on the page: the headline
// is the on-page <h1>, the author and dates appear in the visible byline, the
// image is the featured image shown above the text, and the breadcrumb list
// is built from the same data as the visible breadcrumb trail.

/**
 * Encode data for a JSON-LD script block.
 *
 * Values are left as plain text (no HTML entities) because JSON-LD is parsed as
 * data, not HTML. JSON_HEX_TAG turns "<" and ">" into \u003C / \u003E so the
 * payload can never close the surrounding <script> element.
 *
 * @param array $data Schema.org data.
 * @return string
 */
function bookwp_json_ld( $data ) {
	return '<script type="application/ld+json">'
		. wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP )
		. '</script>' . "\n";
}

/**
 * Plain-text version of a string for structured data.
 *
 * @param string $text Text that may contain tags or entities.
 * @return string
 */
function bookwp_schema_text( $text ) {
	return trim( html_entity_decode( wp_strip_all_tags( (string) $text ), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
}

/**
 * Build Article / BlogPosting data for the current singular post or chapter.
 *
 * @param WP_Post $post Post object.
 * @return array
 */
function bookwp_get_article_schema( $post ) {
	$is_chapter = bookwp_is_chapter_view();

	$data = array(
		'@context'         => 'https://schema.org',
		'@type'            => $is_chapter ? 'Article' : 'BlogPosting',
		'headline'         => bookwp_schema_text( get_the_title( $post ) ),
		'datePublished'    => get_the_date( 'c', $post ),
		'dateModified'     => get_the_modified_date( 'c', $post ),
		'inLanguage'       => get_bloginfo( 'language' ),
		'mainEntityOfPage' => get_permalink( $post ),
	);

	$author_id = (int) $post->post_author;
	if ( $author_id ) {
		$data['author'] = array(
			array(
				'@type' => 'Person',
				'name'  => bookwp_schema_text( get_the_author_meta( 'display_name', $author_id ) ),
				'url'   => get_author_posts_url( $author_id ),
			),
		);
	}

	if ( has_post_thumbnail( $post ) ) {
		$images = array();
		foreach ( array( 'full', 'large' ) as $size ) {
			$src = wp_get_attachment_image_url( get_post_thumbnail_id( $post ), $size );
			if ( $src && ! in_array( $src, $images, true ) ) {
				$images[] = $src;
			}
		}
		if ( $images ) {
			$data['image'] = $images;
		}
	}

	if ( $is_chapter && bookwp_is_pro_mode() ) {
		$terms = get_the_terms( $post, 'book' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$book = reset( $terms );
			$link = get_term_link( $book );
			if ( ! is_wp_error( $link ) ) {
				$data['isPartOf'] = array(
					'@type' => 'Book',
					'name'  => bookwp_schema_text( $book->name ),
					'url'   => $link,
				);
			}
		}
	}

	/**
	 * Filter Article structured data before output.
	 *
	 * @param array   $data Schema.org data.
	 * @param WP_Post $post Current post.
	 */
	return apply_filters( 'bookwp_article_schema', $data, $post );
}

/**
 * Build BreadcrumbList data from the visible breadcrumb trail.
 *
 * @return array Empty when there is no trail.
 */
function bookwp_get_breadcrumb_schema() {
	$items = bookwp_get_breadcrumbs();
	if ( count( $items ) < 2 ) {
		return array();
	}

	$list = array();
	foreach ( array_values( $items ) as $index => $item ) {
		$entry = array(
			'@type'    => 'ListItem',
			'position' => $index + 1,
			'name'     => bookwp_schema_text( $item['name'] ),
		);
		// Google allows the last item (the current page) to omit "item".
		if ( $item['url'] ) {
			$entry['item'] = $item['url'];
		}
		$list[] = $entry;
	}

	return array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list,
	);
}

/**
 * Output structured data on single posts and chapters.
 *
 * Skipped when an SEO plugin is active, on password-protected posts (whose
 * content is not visible), and when disabled via:
 * add_filter( 'bookwp_enable_structured_data', '__return_false' );
 */
function bookwp_structured_data() {
	if ( ! apply_filters( 'bookwp_enable_structured_data', true ) || bookwp_seo_plugin_active() ) {
		return;
	}

	$post_types = bookwp_is_pro_mode() ? array( 'post', 'chapter' ) : array( 'post' );
	if ( ! is_singular( $post_types ) ) {
		return;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post || post_password_required( $post ) ) {
		return;
	}

	echo bookwp_json_ld( bookwp_get_article_schema( $post ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encoded with JSON_HEX_TAG.

	$breadcrumbs = bookwp_get_breadcrumb_schema();
	if ( $breadcrumbs ) {
		echo bookwp_json_ld( $breadcrumbs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encoded with JSON_HEX_TAG.
	}
}
add_action( 'wp_head', 'bookwp_structured_data' );

// ============================================================
// EXTENSIBILITY HOOKS
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
// HEAD CLEANUP
// ============================================================

/**
 * Remove unnecessary <head> links that have no value for a book theme.
 */
function bookwp_head_cleanup() {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
}
add_action( 'init', 'bookwp_head_cleanup' );
