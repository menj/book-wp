<?php
/**
 * Template Name: Book Landing Page
 * Template Post Type: page
 *
 * A full-width landing page for your book — author blurb, cover, description, and CTA.
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
			$tagline = get_bloginfo( 'description' );
			if ( $tagline ) :
				?>
				<p class="book-description"><?php echo esc_html( $tagline ); ?></p>
			<?php endif; ?>
		</div><!-- .book-title-section -->

		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content/content', 'page' );
		endwhile;
		?>

		<nav class="chapter-nav-back-toc" aria-label="<?php esc_attr_e( 'Start reading', 'book-wp' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="chapter-nav-link">
				<?php esc_html_e( 'Read Now →', 'book-wp' ); ?>
			</a>
		</nav>

	</div><!-- .content-area -->
</main><!-- #main-content -->

<?php get_footer(); ?>
