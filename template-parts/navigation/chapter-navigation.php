<?php
/**
 * Chapter navigation template part (prev / next links).
 *
 * Optional overrides can be passed through get_template_part()'s $args:
 *
 *     get_template_part(
 *         'template-parts/navigation/chapter-navigation',
 *         null,
 *         array( 'prev' => $prev_post, 'next' => $next_post )
 *     );
 *
 * @package book-wp
 *
 * @var array $args Optional. Keys 'prev' and 'next' accept WP_Post or null.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bookwp_post_id = get_the_ID();
if ( ! $bookwp_post_id ) {
	return;
}

$bookwp_adjacent = bookwp_get_adjacent_chapters( $bookwp_post_id );
$bookwp_prev     = $bookwp_adjacent['prev'];
$bookwp_next     = $bookwp_adjacent['next'];

// Allow overrides passed via get_template_part( ..., ..., $args ).
if ( isset( $args ) && is_array( $args ) ) {
	if ( array_key_exists( 'prev', $args ) ) {
		$bookwp_prev = $args['prev'] instanceof WP_Post ? $args['prev'] : null;
	}
	if ( array_key_exists( 'next', $args ) ) {
		$bookwp_next = $args['next'] instanceof WP_Post ? $args['next'] : null;
	}
}

if ( ! $bookwp_prev && ! $bookwp_next ) {
	return;
}

$bookwp_toc_url   = home_url( '/' );
$bookwp_toc_label = __( 'Back to Table of Contents', 'book-wp' );
if ( bookwp_is_pro_mode() ) {
	$bookwp_book_terms = get_the_terms( $bookwp_post_id, 'book' );
	if ( $bookwp_book_terms && ! is_wp_error( $bookwp_book_terms ) ) {
		$bookwp_book_term = reset( $bookwp_book_terms );
		$bookwp_book_link = get_term_link( $bookwp_book_term );
		if ( ! is_wp_error( $bookwp_book_link ) ) {
			$bookwp_toc_url = $bookwp_book_link;
			/* translators: %s: book title */
			$bookwp_toc_label = sprintf( __( 'Back to %s contents', 'book-wp' ), $bookwp_book_term->name );
		}
	}
}
?>
<nav class="chapter-navigation" aria-label="<?php esc_attr_e( 'Chapter navigation', 'book-wp' ); ?>">
	<div class="chapter-nav-links">

		<?php if ( $bookwp_prev ) : ?>
			<a
				class="chapter-nav-link nav-prev"
				href="<?php echo esc_url( get_permalink( $bookwp_prev->ID ) ); ?>"
				rel="prev"
			>
				<span class="chapter-nav-label" aria-hidden="true">&#8592; <?php esc_html_e( 'Previous', 'book-wp' ); ?></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Previous chapter:', 'book-wp' ); ?></span>
				<span class="chapter-nav-title"><?php echo esc_html( get_the_title( $bookwp_prev->ID ) ); ?></span>
			</a>
		<?php endif; ?>

		<?php if ( $bookwp_next ) : ?>
			<a
				class="chapter-nav-link nav-next"
				href="<?php echo esc_url( get_permalink( $bookwp_next->ID ) ); ?>"
				rel="next"
			>
				<span class="chapter-nav-label" aria-hidden="true"><?php esc_html_e( 'Next', 'book-wp' ); ?> &#8594;</span>
				<span class="screen-reader-text"><?php esc_html_e( 'Next chapter:', 'book-wp' ); ?></span>
				<span class="chapter-nav-title"><?php echo esc_html( get_the_title( $bookwp_next->ID ) ); ?></span>
			</a>
		<?php endif; ?>

	</div><!-- .chapter-nav-links -->

	<p class="chapter-nav-back-toc">
		<a href="<?php echo esc_url( $bookwp_toc_url ); ?>">
			&larr; <?php echo esc_html( $bookwp_toc_label ); ?>
		</a>
	</p>

</nav><!-- .chapter-navigation -->
