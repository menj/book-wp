<?php
/**
 * Chapter navigation template part (prev / next links).
 *
 * @package book-wp
 *
 * @var WP_Post|null $prev_chapter Optional. Override prev chapter.
 * @var WP_Post|null $next_chapter Optional. Override next chapter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();
if ( ! $post_id ) {
	return;
}

$adjacent = bookwp_get_adjacent_chapters( $post_id );
$prev     = $adjacent['prev'];
$next     = $adjacent['next'];

// Allow template override via passed variables.
if ( isset( $prev_chapter ) ) {
	$prev = $prev_chapter;
}
if ( isset( $next_chapter ) ) {
	$next = $next_chapter;
}

if ( ! $prev && ! $next ) {
	return;
}

$toc_url = home_url( '/' );
?>
<nav class="chapter-navigation" aria-label="<?php esc_attr_e( 'Chapter navigation', 'book-wp' ); ?>">
	<div class="chapter-nav-links">

		<?php if ( $prev ) : ?>
			<a
				class="chapter-nav-link nav-prev"
				href="<?php echo esc_url( get_permalink( $prev->ID ) ); ?>"
				rel="prev"
			>
				<span class="chapter-nav-label" aria-hidden="true">&#8592; <?php esc_html_e( 'Previous', 'book-wp' ); ?></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Previous chapter:', 'book-wp' ); ?></span>
				<span class="chapter-nav-title"><?php echo esc_html( get_the_title( $prev->ID ) ); ?></span>
			</a>
		<?php endif; ?>

		<?php if ( $next ) : ?>
			<a
				class="chapter-nav-link nav-next"
				href="<?php echo esc_url( get_permalink( $next->ID ) ); ?>"
				rel="next"
			>
				<span class="chapter-nav-label" aria-hidden="true"><?php esc_html_e( 'Next', 'book-wp' ); ?> &#8594;</span>
				<span class="screen-reader-text"><?php esc_html_e( 'Next chapter:', 'book-wp' ); ?></span>
				<span class="chapter-nav-title"><?php echo esc_html( get_the_title( $next->ID ) ); ?></span>
			</a>
		<?php endif; ?>

	</div><!-- .chapter-nav-links -->

	<p class="chapter-nav-back-toc">
		<a href="<?php echo esc_url( $toc_url ); ?>">
			&larr; <?php esc_html_e( 'Back to Table of Contents', 'book-wp' ); ?>
		</a>
	</p>

</nav><!-- .chapter-navigation -->
