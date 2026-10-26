<?php
/**
 * Generic post content template part.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">
		<?php if ( is_singular() ) : ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php else : ?>
			<h2 class="entry-title">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2>
		<?php endif; ?>
		<?php get_template_part( 'template-parts/global/metadata' ); ?>
	</header><!-- .entry-header -->

	<?php
	if ( is_singular() ) {
		get_template_part( 'template-parts/global/featured-image' );
	}
	?>

	<div class="entry-content">
		<?php
		if ( is_singular() ) {
			the_content();
			wp_link_pages(
				array(
					'before'         => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'book-wp' ) . '"><span class="page-links-title">' . esc_html__( 'Pages:', 'book-wp' ) . '</span>',
					'after'          => '</nav>',
					'next_or_number' => 'number',
				)
			);
		} else {
			the_excerpt();
		}
		?>
	</div><!-- .entry-content -->

	<?php edit_post_link( esc_html__( 'Edit this entry', 'book-wp' ), '<footer class="entry-footer"><p>', '</p></footer>' ); ?>

</article><!-- .hentry -->
