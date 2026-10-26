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
	$author = get_the_author();
	if ( $author ) :
		?>
		<span class="entry-author">
			<?php
			printf(
				/* translators: %s: author name */
				esc_html__( 'by %s', 'book-wp' ),
				esc_html( $author )
			);
			?>
		</span>
	<?php endif; ?>
</div><!-- .entry-meta -->
