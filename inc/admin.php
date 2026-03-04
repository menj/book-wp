<?php
/**
 * Admin Notices and Legacy Options Page.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ============================================================
// 11. ADMIN NOTICES — missing chapter_number
// ============================================================

/**
 * Show a non-fatal admin notice when chapter posts are missing
 * the chapter_number custom field (Default Mode only).
 */
function bookwp_admin_notice_chapter_number() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->base, array( 'edit', 'post' ), true ) ) {
		return;
	}

	// Pro Mode uses menu_order; chapter_number is irrelevant there.
	if ( bookwp_is_pro_mode() ) {
		return;
	}

	$cat_id = bookwp_get_chapter_category_id();
	if ( ! $cat_id ) {
		return;
	}

	$missing = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 5,
			'cat'            => $cat_id,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'chapter_number',
					'compare' => 'NOT EXISTS',
				),
			),
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $missing ) ) {
		?>
		<div class="notice notice-warning bookwp-admin-notice" role="note">
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: 1: number of posts, 2: edit URL */
						_n(
							'<strong>Book WP:</strong> %1$d chapter post is missing the <code>chapter_number</code> custom field. Without it, chapters fall back to date ordering. <a href="%2$s">Edit posts</a>',
							'<strong>Book WP:</strong> %1$d chapter posts are missing the <code>chapter_number</code> custom field. Without it, chapters fall back to date ordering. <a href="%2$s">Edit posts</a>',
							count( $missing ),
							'book-wp'
						),
						count( $missing ),
						esc_url( admin_url( 'edit.php?cat=' . intval( $cat_id ) ) )
					)
				);
				?>
			</p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'bookwp_admin_notice_chapter_number' );

// ============================================================
// 12. LEGACY OPTIONS PAGE — replaced by Customizer
// ============================================================

/**
 * Register the legacy Book WP Options submenu page.
 * The page still appears for backward compatibility but saves nothing.
 */
function bookwp_legacy_options_page() {
	add_submenu_page(
		'themes.php',
		esc_html__( 'Book WP Options', 'book-wp' ),
		esc_html__( 'Book WP Options', 'book-wp' ),
		'manage_options',
		'book-wp-options',
		'bookwp_legacy_options_redirect'
	);
}
add_action( 'admin_menu', 'bookwp_legacy_options_page' );

/**
 * Render the legacy options page as a redirect notice pointing to the Customizer.
 */
function bookwp_legacy_options_redirect() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'book-wp' ) );
	}
	$customizer_url = admin_url( 'customize.php?autofocus[section]=bookwp_book_settings' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Book WP Options', 'book-wp' ); ?></h1>
		<div class="notice notice-info" role="note">
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: %s: Customizer URL */
						__( 'Book WP settings have moved to the <a href="%s">Customizer → Book Settings</a>. No settings are saved here.', 'book-wp' ),
						esc_url( $customizer_url )
					)
				);
				?>
			</p>
		</div>
		<p>
			<a href="<?php echo esc_url( $customizer_url ); ?>" class="button button-primary">
				<?php esc_html_e( 'Open Customizer → Book Settings', 'book-wp' ); ?>
			</a>
		</p>
	</div>
	<?php
}
