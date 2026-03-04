<?php
/**
 * Chapter content template part.
 * Used in single.php (Default Mode) and single-chapter.php (Pro Mode).
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chapter_number = get_post_meta( get_the_ID(), 'chapter_number', true );
?>

<article <?php post_class( 'chapter-article' ); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">
		<?php if ( $chapter_number ) : ?>
			<span class="chapter-number-label">
				<?php
				printf(
					/* translators: %s: chapter number */
					esc_html__( 'Chapter %s', 'book-wp' ),
					esc_html( $chapter_number )
				);
				?>
			</span>
		<?php endif; ?>
		<h1 class="entry-title chapter-title"><?php the_title(); ?></h1>
	</header><!-- .entry-header -->

	<?php bookwp_before_chapter_content( get_post() ); ?>

	<div class="entry-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before'         => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'book-wp' ) . '"><span class="page-links-title">' . esc_html__( 'Pages:', 'book-wp' ) . '</span>',
				'after'          => '</nav>',
				'link_before'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'book-wp' ) . ' </span>',
				'next_or_number' => 'number',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php bookwp_after_chapter_content( get_post() ); ?>

	<?php edit_post_link( esc_html__( 'Edit this chapter', 'book-wp' ), '<footer class="entry-footer"><p>', '</p></footer>' ); ?>

</article><!-- .chapter-article -->
