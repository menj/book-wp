<?php
/**
 * Single chapter template (Pro Mode).
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
			get_template_part( 'template-parts/content/content', 'chapter' );
			get_template_part( 'template-parts/navigation/chapter-navigation' );

			if ( (int) bookwp_get_option( 'bookwp_show_comments', 0 ) && comments_open() ) :
				?>
				<section class="comments-section">
					<?php comments_template(); ?>
				</section>
				<?php
			endif;
		endwhile;
		?>

	</div><!-- .content-area -->
</main><!-- #main-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
