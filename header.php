<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page-wrap" class="site">

	<a class="skip-link" href="#main-content"><?php esc_html_e( 'Skip to main content', 'book-wp' ); ?></a>

	<header id="header" class="site-header" role="banner">
		<div class="site-header-inner">

			<?php get_template_part( 'template-parts/global/site-branding' ); ?>

		</div><!-- .site-header-inner -->
	</header><!-- #header -->
