<?php
/**
 * Site branding and primary navigation template part.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="site-branding">
	<p class="site-title">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php bloginfo( 'name' ); ?>
		</a>
	</p>

	<?php
	$description = get_bloginfo( 'description' );
	if ( $description ) :
		?>
		<p class="site-description"><?php echo esc_html( $description ); ?></p>
	<?php endif; ?>
</div><!-- .site-branding -->

<?php
// Primary navigation — only rendered when a menu is assigned in Appearance → Menus.
if ( has_nav_menu( 'primary' ) ) :
	wp_nav_menu(
		array(
			'theme_location'  => 'primary',
			'menu_id'         => 'primary-menu',
			'menu_class'      => 'primary-nav-list',
			'container'       => 'nav',
			'container_id'    => 'primary-navigation',
			'container_class' => 'primary-navigation',
			'items_wrap'      => '<ul id="%1$s" class="%2$s" role="list">%3$s</ul>',
			'fallback_cb'     => false,
			'depth'           => 2,
		)
	);
endif;
?>

<?php
// Chapter blurb: shown on chapter single pages only.
$chapter_blurb = bookwp_get_option( 'bookwp_chapter_blurb', '' );
$is_chapter    = (
	( ! bookwp_is_pro_mode() && is_singular( 'post' ) && has_category( 'chapter' ) ) ||
	( bookwp_is_pro_mode() && is_singular( 'chapter' ) )
);
if ( $chapter_blurb && $is_chapter ) :
	?>
	<div class="chapter-blurb" id="chapter-blurb">
		<?php echo wp_kses_post( $chapter_blurb ); ?>
	</div>
<?php endif; ?>
