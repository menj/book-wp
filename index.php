<?php
/**
 * Front page / Table of Contents template.
 * Default Mode: shows book title, optional front blurb, and TOC of chapter-category posts.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="site-main" role="main" tabindex="-1">
	<div class="content-area">

		<?php if ( is_home() || is_archive() ) : ?>
			<header class="archive-header">
				<h1 class="archive-title">
					<?php
					if ( is_home() ) {
						$bookwp_posts_page = (int) get_option( 'page_for_posts' );
						echo esc_html( $bookwp_posts_page ? get_the_title( $bookwp_posts_page ) : get_bloginfo( 'name' ) );
					} else {
						echo wp_kses_post( get_the_archive_title() );
					}
					?>
				</h1>
				<?php
				$bookwp_archive_desc = is_archive() ? get_the_archive_description() : '';
				if ( $bookwp_archive_desc ) :
					?>
					<div class="archive-description"><?php echo wp_kses_post( $bookwp_archive_desc ); ?></div>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/content/content' );
			}
			the_posts_pagination(
				array(
					'aria_label'         => __( 'Posts', 'book-wp' ),
					'prev_text'          => '<span aria-hidden="true">&laquo;</span> ' . esc_html__( 'Newer posts', 'book-wp' ),
					'next_text'          => esc_html__( 'Older posts', 'book-wp' ) . ' <span aria-hidden="true">&raquo;</span>',
					'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'book-wp' ) . ' </span>',
				)
			);
		} else {
			get_template_part( 'template-parts/navigation/toc' );
		}
		?>

	</div><!-- .content-area -->
</main><!-- #main-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
