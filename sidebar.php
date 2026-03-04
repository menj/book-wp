<?php
/**
 * Sidebar template.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'sidebar-widgets' ) ) {
	return;
}
?>

<aside id="sidebar" class="sidebar widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Sidebar', 'book-wp' ); ?>">
	<?php dynamic_sidebar( 'sidebar-widgets' ); ?>
</aside><!-- #sidebar -->
