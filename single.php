<?php
/**
 * Single post template.
 * Renders chapter content when the post is in the 'chapter' category (Default Mode),
 * or falls back to generic content.
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

		<?php
		while ( have_posts() ) :
			the_post();

			$is_chapter = has_category( 'chapter' ) && ! bookwp_is_pro_mode();

			if ( $is_chapter ) {
				get_template_part( 'template-parts/content/content', 'chapter' );
				get_template_part( 'template-parts/navigation/chapter-navigation' );

				// Comments.
				if ( (int) bookwp_get_option( 'show_comments' ) && comments_open() ) :
					?>
					<section class="comments-section">
						<?php comments_template(); ?>
					</section>
					<?php
				endif;
			} else {
				get_template_part( 'template-parts/content/content' );
				if ( comments_open() || get_comments_number() ) :
					?>
					<section class="comments-section">
						<?php comments_template(); ?>
					</section>
					<?php
				endif;
			}

		endwhile;
		?>

	</div><!-- .content-area -->
</main><!-- #main-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
