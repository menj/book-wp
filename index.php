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

		<?php
		$front_blurb = bookwp_get_option( 'bookwp_front_blurb', '' );
		if ( $front_blurb ) :
			?>
			<div class="front-blurb" id="front-blurb">
				<?php echo wp_kses_post( $front_blurb ); ?>
			</div>
		<?php endif; ?>

		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/content/content' );
			}
		} else {
			// Default: render TOC of chapters.
			get_template_part( 'template-parts/navigation/toc' );
		}
		?>

	</div><!-- .content-area -->
</main><!-- #main-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
