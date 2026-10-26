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
			?>
			<div class="entry-content page-content">
				<?php the_content(); ?>
			</div>
			<?php
		endwhile;

		$bookwp_chapters = bookwp_is_pro_mode() ? bookwp_get_pro_chapter_posts() : bookwp_get_chapter_posts();
		$bookwp_first    = $bookwp_chapters ? reset( $bookwp_chapters ) : null;
		if ( $bookwp_first ) :
			?>
			<p class="read-now">
				<a class="read-now__link" href="<?php echo esc_url( get_permalink( $bookwp_first ) ); ?>">
					<?php
					printf(
						/* translators: %s: title of the first chapter */
						esc_html__( 'Start reading: %s', 'book-wp' ),
						esc_html( get_the_title( $bookwp_first ) )
					);
					?>
				</a>
			</p>
		<?php endif; ?>

	</div><!-- .content-area -->
</main><!-- #main-content -->

<?php get_footer(); ?>
