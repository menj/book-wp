<?php
/**
 * Table of Contents template part.
 * Used in Default Mode (posts with 'chapter' category).
 * For Pro Mode see taxonomy-book.php.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bookwp_current_post_id = get_the_ID();

// In Pro Mode, chapters are the 'chapter' CPT; Default Mode uses category posts.
if ( bookwp_is_pro_mode() ) {
	// When loaded outside a book taxonomy context, show all chapters unfiltered.
	$bookwp_queried  = get_queried_object();
	$bookwp_book_id  = ( $bookwp_queried instanceof WP_Term && 'book' === $bookwp_queried->taxonomy )
		? $bookwp_queried->term_id
		: null;
	$bookwp_chapters = bookwp_get_pro_chapter_posts( $bookwp_book_id );
} else {
	$bookwp_chapters = bookwp_get_chapter_posts();
}

if ( empty( $bookwp_chapters ) ) {
	return;
}
?>
<nav class="toc-container" aria-label="<?php esc_attr_e( 'Table of Contents', 'book-wp' ); ?>">
	<h2 class="toc-heading" id="toc-heading"><?php esc_html_e( 'Table of Contents', 'book-wp' ); ?></h2>
	<ol class="toc-list" aria-labelledby="toc-heading">
		<?php foreach ( $bookwp_chapters as $bookwp_chapter ) : ?>
			<li>
				<a
					href="<?php echo esc_url( get_permalink( $bookwp_chapter->ID ) ); ?>"
					<?php if ( $bookwp_current_post_id && $bookwp_current_post_id === $bookwp_chapter->ID ) : ?>
					aria-current="page"
					<?php endif; ?>
				>
					<?php echo esc_html( get_the_title( $bookwp_chapter->ID ) ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ol>
</nav><!-- .toc-container -->
