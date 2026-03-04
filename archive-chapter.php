<?php
/**
 * Chapter archive template (Pro Mode).
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

		<header class="archive-header book-archive-header">
			<h1><?php esc_html_e( 'All Chapters', 'book-wp' ); ?></h1>
		</header>

		<?php get_template_part( 'template-parts/navigation/toc' ); ?>

	</div><!-- .content-area -->
</main><!-- #main-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
