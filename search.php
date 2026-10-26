<?php
/**
 * Search results template.
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

		<header class="archive-header">
			<h1>
				<?php
				printf(
					/* translators: %s: search term */
					esc_html__( 'Search Results for: %s', 'book-wp' ),
					'<span>' . esc_html( get_search_query() ) . '</span>'
				);
				?>
			</h1>
		</header>

		<?php if ( have_posts() ) : ?>

			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<?php get_template_part( 'template-parts/content/content' ); ?>
			<?php endwhile; ?>

			<?php
			the_posts_pagination(
				array(
					'aria_label'         => __( 'Search results pages', 'book-wp' ),
					'prev_text'          => '<span aria-hidden="true">&laquo;</span><span class="screen-reader-text">' . esc_html__( 'Previous page', 'book-wp' ) . '</span>',
					'next_text'          => '<span aria-hidden="true">&raquo;</span><span class="screen-reader-text">' . esc_html__( 'Next page', 'book-wp' ) . '</span>',
					'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'book-wp' ) . ' </span>',
				)
			);
			?>

		<?php else : ?>

			<section class="no-results-message">
				<p>
					<?php
					printf(
						/* translators: %s: search query */
						esc_html__( 'Nothing found for &#8220;%s&#8221;. Try a different search.', 'book-wp' ),
						esc_html( get_search_query() )
					);
					?>
				</p>
				<?php get_search_form(); ?>
			</section>

		<?php endif; ?>

	</div><!-- .content-area -->
</main><!-- #main-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
