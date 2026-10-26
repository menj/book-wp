<?php
/**
 * Admin Notices.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Transient key for the cached "missing chapter_number" count.
 */
const BOOKWP_MISSING_META_TRANSIENT = 'bookwp_missing_chapter_number';

/**
 * Count published chapter posts missing the chapter_number meta (Default Mode).
 * Cached in a transient for an hour and invalidated whenever a post is saved.
 *
 * @return int
 */
function bookwp_count_posts_missing_chapter_number() {
	$cached = get_transient( BOOKWP_MISSING_META_TRANSIENT );
	if ( false !== $cached ) {
		return (int) $cached;
	}

	$count  = 0;
	$cat_id = bookwp_get_chapter_category_id();
	if ( $cat_id ) {
		$query = new WP_Query(
			array(
				'post_type'              => 'post',
				'post_status'            => 'publish',
				'posts_per_page'         => 1,
				'cat'                    => $cat_id,
				'fields'                 => 'ids',
				'no_found_rows'          => false,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'     => 'chapter_number',
						'compare' => 'NOT EXISTS',
					),
				),
			)
		);
		$count = (int) $query->found_posts;
	}

	set_transient( BOOKWP_MISSING_META_TRANSIENT, $count, HOUR_IN_SECONDS );
	return $count;
}

/**
 * Invalidate the cached count when posts change.
 */
function bookwp_clear_missing_chapter_number_cache() {
	delete_transient( BOOKWP_MISSING_META_TRANSIENT );
}
add_action( 'save_post_post', 'bookwp_clear_missing_chapter_number_cache' );
add_action( 'deleted_post', 'bookwp_clear_missing_chapter_number_cache' );
add_action( 'trashed_post', 'bookwp_clear_missing_chapter_number_cache' );
add_action( 'updated_post_meta', 'bookwp_clear_missing_chapter_number_cache' );
add_action( 'added_post_meta', 'bookwp_clear_missing_chapter_number_cache' );
add_action( 'deleted_post_meta', 'bookwp_clear_missing_chapter_number_cache' );
add_action( 'set_object_terms', 'bookwp_clear_missing_chapter_number_cache' );

/**
 * Show a non-fatal admin notice when chapter posts are missing
 * the chapter_number custom field (Default Mode only).
 */
function bookwp_admin_notice_chapter_number() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->base, array( 'edit', 'post' ), true ) ) {
		return;
	}

	// Pro Mode uses menu_order; chapter_number is irrelevant there.
	if ( bookwp_is_pro_mode() ) {
		return;
	}

	$missing = bookwp_count_posts_missing_chapter_number();
	if ( $missing < 1 ) {
		return;
	}

	$cat_id = bookwp_get_chapter_category_id();
	?>
	<div class="notice notice-warning bookwp-admin-notice" role="note">
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					/* translators: 1: number of posts, 2: edit URL */
					_n(
						'<strong>Book WP:</strong> %1$d chapter post is missing the <code>chapter_number</code> custom field. Without it, chapters fall back to date ordering. <a href="%2$s">Edit posts</a>',
						'<strong>Book WP:</strong> %1$d chapter posts are missing the <code>chapter_number</code> custom field. Without it, chapters fall back to date ordering. <a href="%2$s">Edit posts</a>',
						$missing,
						'book-wp'
					),
					$missing,
					esc_url( admin_url( 'edit.php?cat=' . intval( $cat_id ) ) )
				)
			);
			?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'bookwp_admin_notice_chapter_number' );
