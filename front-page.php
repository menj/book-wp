<?php
/**
 * Front page template.
 * Rendered when "Your homepage displays: A static page" points here,
 * or as the default front page.
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

		<div class="book-title-section">
			<h1><?php bloginfo( 'name' ); ?></h1>
			<?php
			$bookwp_tagline = get_bloginfo( 'description' );
			if ( $bookwp_tagline ) :
				?>
				<p class="book-description"><?php echo esc_html( $bookwp_tagline ); ?></p>
			<?php endif; ?>
		</div><!-- .book-title-section -->

		<?php
		$bookwp_front_blurb = bookwp_get_option( 'front_blurb' );
		if ( $bookwp_front_blurb ) :
			?>
			<div class="front-blurb" id="front-blurb">
				<?php echo wp_kses_post( $bookwp_front_blurb ); ?>
			</div>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/navigation/toc' ); ?>

	</div><!-- .content-area -->
</main><!-- #main-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
