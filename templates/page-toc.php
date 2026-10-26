<?php
/**
 * Template Name: Table of Contents
 * Template Post Type: page
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

		<header class="page-header">
			<h1><?php the_title(); ?></h1>
		</header>

		<?php
		$bookwp_front_blurb = bookwp_get_option( 'front_blurb' );
		if ( $bookwp_front_blurb ) :
			?>
			<div class="front-blurb">
				<?php echo wp_kses_post( $bookwp_front_blurb ); ?>
			</div>
		<?php endif; ?>

		<?php
		if ( bookwp_is_pro_mode() ) {
			// In Pro Mode, list all books with links to their taxonomy pages.
			$bookwp_books = get_terms(
				array(
					'taxonomy'   => 'book',
					'hide_empty' => true,
				)
			);
			if ( $bookwp_books && ! is_wp_error( $bookwp_books ) ) :
				?>
				<nav aria-label="<?php esc_attr_e( 'Books list', 'book-wp' ); ?>">
					<h2><?php esc_html_e( 'Books', 'book-wp' ); ?></h2>
					<ul class="toc-list">
						<?php foreach ( $bookwp_books as $bookwp_book ) : ?>
							<li>
								<a href="<?php echo esc_url( get_term_link( $bookwp_book ) ); ?>">
									<?php echo esc_html( $bookwp_book->name ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
				<?php
			endif;
		} else {
			get_template_part( 'template-parts/navigation/toc' );
		}
		?>

	</div><!-- .content-area -->
</main><!-- #main-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
