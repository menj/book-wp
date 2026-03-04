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

$current_post_id = get_the_ID();

// In Pro Mode, chapters are the 'chapter' CPT; Default Mode uses category posts.
if ( bookwp_is_pro_mode() ) {
	// When loaded outside a book taxonomy context, show all chapters unfiltered.
	$queried = get_queried_object();
	$book_id = ( $queried instanceof WP_Term && 'book' === $queried->taxonomy )
		? $queried->term_id
		: null;
	$chapters = bookwp_get_pro_chapter_posts( $book_id );
} else {
	$chapters = bookwp_get_chapter_posts();
}

if ( empty( $chapters ) ) {
	return;
}
?>
<nav class="toc-container" aria-label="<?php esc_attr_e( 'Table of Contents', 'book-wp' ); ?>">
	<h2 class="toc-heading" id="toc-heading"><?php esc_html_e( 'Table of Contents', 'book-wp' ); ?></h2>
	<ol class="toc-list" aria-labelledby="toc-heading">
		<?php foreach ( $chapters as $chapter ) : ?>
			<li>
				<a
					href="<?php echo esc_url( get_permalink( $chapter->ID ) ); ?>"
					<?php if ( $current_post_id && $current_post_id === $chapter->ID ) : ?>
					aria-current="page"
					<?php endif; ?>
				>
					<?php echo esc_html( get_the_title( $chapter->ID ) ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ol>
</nav><!-- .toc-container -->
