<?php
/**
 * Chapter Query Helpers.
 *
 * Provides ordered chapter lists and prev/next adjacency for both Default Mode
 * (posts in the 'chapter' category) and Pro Mode (the 'chapter' CPT).
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ============================================================
// CHAPTER NAVIGATION HELPERS
// ============================================================

/**
 * Return all published chapter posts in correct reading order (Default Mode).
 *
 * Uses named meta_query clauses so that posts without a chapter_number meta
 * are included and fall back to date ordering — a bare 'meta_key' arg would
 * generate an INNER JOIN that silently drops those posts.
 *
 * @return WP_Post[]
 */
function bookwp_get_chapter_posts() {
	$args = apply_filters(
		'bookwp_toc_query_args',
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'cat'            => bookwp_get_chapter_category_id(),
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation'           => 'OR',
				'chapter_num_clause' => array(
					'key'     => 'chapter_number',
					'compare' => 'EXISTS',
				),
				'no_chapter_clause'  => array(
					'key'     => 'chapter_number',
					'compare' => 'NOT EXISTS',
				),
			),
			'orderby'        => array(
				'chapter_num_clause' => 'ASC',
				'date'               => 'ASC',
			),
		)
	);

	return get_posts( $args );
}

/**
 * Return the term_id of the 'chapter' category, or false if it doesn't exist.
 *
 * @return int|false
 */
function bookwp_get_chapter_category_id() {
	$term = get_term_by( 'slug', 'chapter', 'category' );
	if ( $term && ! is_wp_error( $term ) ) {
		return $term->term_id;
	}
	return false;
}

/**
 * Return the ordered chapter list that $post_id belongs to, in either mode.
 *
 * In Pro Mode the list is scoped to the chapter's first assigned Book term;
 * in Default Mode it is every post in the 'chapter' category.
 *
 * @param int $post_id Current post ID.
 * @return WP_Post[]
 */
function bookwp_get_chapter_sequence( $post_id ) {
	if ( bookwp_is_pro_mode() ) {
		$book_terms = get_the_terms( $post_id, 'book' );
		$book_term  = ( $book_terms && ! is_wp_error( $book_terms ) ) ? reset( $book_terms ) : null;
		return bookwp_get_pro_chapter_posts( $book_term ? $book_term->term_id : null );
	}
	return bookwp_get_chapter_posts();
}

/**
 * Return the prev/next chapter posts relative to $post_id (either mode).
 *
 * @param int $post_id Current post ID.
 * @return array{prev: WP_Post|null, next: WP_Post|null}
 */
function bookwp_get_adjacent_chapters( $post_id ) {
	$chapters = bookwp_get_chapter_sequence( (int) $post_id );
	$ids      = array_map( 'intval', wp_list_pluck( $chapters, 'ID' ) );
	$index    = array_search( (int) $post_id, $ids, true );

	return array(
		'prev' => ( false !== $index && $index > 0 ) ? $chapters[ $index - 1 ] : null,
		'next' => ( false !== $index && $index < count( $chapters ) - 1 ) ? $chapters[ $index + 1 ] : null,
	);
}

/**
 * Return all published chapter posts in correct reading order (Pro Mode).
 *
 * Ordered by menu_order (Page Attributes), then chapter_number meta,
 * then date — all as a graceful cascade.
 *
 * @param int|string|null $book_term_id Optional. Book term ID or slug to filter by.
 * @return WP_Post[]
 */
function bookwp_get_pro_chapter_posts( $book_term_id = null ) {
	$args = array(
		'post_type'      => 'chapter',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'relation'           => 'OR',
			'chapter_num_clause' => array(
				'key'     => 'chapter_number',
				'compare' => 'EXISTS',
			),
			'no_chapter_clause'  => array(
				'key'     => 'chapter_number',
				'compare' => 'NOT EXISTS',
			),
		),
		'orderby'        => array(
			'menu_order'         => 'ASC',
			'chapter_num_clause' => 'ASC',
			'date'               => 'ASC',
		),
	);

	if ( $book_term_id ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'book',
				'field'    => is_numeric( $book_term_id ) ? 'id' : 'slug',
				'terms'    => $book_term_id,
			),
		);
	}

	$args = apply_filters( 'bookwp_pro_toc_query_args', $args );

	return get_posts( $args );
}
