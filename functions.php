<?php
/**
 * Book WP — functions.php
 *
 * This file is a loader only. All theme logic lives in inc/.
 *
 * Load order matters: options.php (defines bookwp_get_option, the option
 * schema, and the font map) must be loaded before everything else, and
 * pro-mode.php (defines bookwp_is_pro_mode) must be loaded before
 * chapters.php, admin.php and theme-options.php.
 *
 * @package book-wp
 * @license GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme version, read once from the style.css header so that asset cache-busting
 * and inline version strings never drift from the declared theme version.
 */
if ( ! defined( 'BOOKWP_VERSION' ) ) {
	$bookwp_theme = wp_get_theme( get_template() );
	define( 'BOOKWP_VERSION', $bookwp_theme->exists() ? (string) $bookwp_theme->get( 'Version' ) : '2.1.0' );
	unset( $bookwp_theme );
}

$bookwp_includes = array(
	'inc/options.php',         // Option schema, bookwp_get_option(), font map, colour schemes — loaded first.
	'inc/pro-mode.php',        // bookwp_is_pro_mode() — needed by setup, chapters, admin.
	'inc/setup.php',           // Theme supports, enqueue, body class, sidebar.
	'inc/customizer.php',      // Customizer panel + sections (live preview surface for the same options).
	'inc/chapters.php',        // Chapter query helpers.
	'inc/admin.php',           // Admin notices.
	'inc/theme-options.php',   // Appearance → Theme Options settings page (tabbed).
	'inc/seo.php',             // Meta description, canonical, breadcrumbs.
	'inc/structured-data.php', // JSON-LD, extensibility hooks, head cleanup.
);

foreach ( $bookwp_includes as $bookwp_file ) {
	require_once get_template_directory() . '/' . $bookwp_file;
}
unset( $bookwp_includes, $bookwp_file );
