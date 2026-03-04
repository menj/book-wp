<?php
/**
 * Page content template part.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</header><!-- .entry-header -->

	<div class="entry-content page-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before'         => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'book-wp' ) . '"><span class="page-links-title">' . esc_html__( 'Pages:', 'book-wp' ) . '</span>',
				'after'          => '</nav>',
				'next_or_number' => 'number',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php edit_post_link( esc_html__( 'Edit this page', 'book-wp' ), '<footer class="entry-footer"><p>', '</p></footer>' ); ?>

</article><!-- .page -->
