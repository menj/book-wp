<?php
/**
 * 404 template.
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
			<h1><?php esc_html_e( 'Page Not Found', 'book-wp' ); ?></h1>
		</header>

		<section class="error-404-message">
			<p><?php esc_html_e( 'The page you were looking for could not be found. It may have been moved, deleted, or perhaps the URL was mistyped.', 'book-wp' ); ?></p>
			<p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Return to the Table of Contents', 'book-wp' ); ?>
				</a>
			</p>
			<?php get_search_form(); ?>
		</section>

		<?php get_template_part( 'template-parts/navigation/toc' ); ?>

	</div><!-- .content-area -->
</main><!-- #main-content -->

<?php get_footer(); ?>
