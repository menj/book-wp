<?php
/**
 * Post metadata template part.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="entry-meta">
	<time class="entry-date published" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
		<?php
		printf(
			/* translators: %s: date string */
			esc_html__( 'Published %s', 'book-wp' ),
			esc_html( get_the_date() )
		);
		?>
	</time>
	<?php
	$bookwp_author = get_the_author();
	if ( $bookwp_author ) :
		?>
		<span class="entry-author">
			<?php
			printf(
				/* translators: %s: author name, linked to the author archive */
				esc_html__( 'by %s', 'book-wp' ),
				'<a class="url fn n" href="' . esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ) . '" rel="author">' . esc_html( $bookwp_author ) . '</a>'
			);
			?>
		</span>
	<?php endif; ?>
</div><!-- .entry-meta -->
