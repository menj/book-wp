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
		$front_blurb = bookwp_get_option( 'bookwp_front_blurb', '' );
		if ( $front_blurb ) :
			?>
			<div class="front-blurb">
				<?php echo wp_kses_post( $front_blurb ); ?>
			</div>
		<?php endif; ?>

		<?php
		if ( bookwp_is_pro_mode() ) {
			// In Pro Mode, list all books with links to their taxonomy pages.
			$books = get_terms(
				array(
					'taxonomy' => 'book',
					'hide_empty' => true,
				)
			);
			if ( $books && ! is_wp_error( $books ) ) :
				?>
				<nav aria-label="<?php esc_attr_e( 'Books list', 'book-wp' ); ?>">
					<h2><?php esc_html_e( 'Books', 'book-wp' ); ?></h2>
					<ul class="toc-list">
						<?php foreach ( $books as $book ) : ?>
							<li>
								<a href="<?php echo esc_url( get_term_link( $book ) ); ?>">
									<?php echo esc_html( $book->name ); ?>
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
