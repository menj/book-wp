<?php
/**
 * Book WP — functions.php
 *
 * This file is a loader only. All theme logic lives in inc/.
 *
 * Load order matters: customizer.php (defines bookwp_get_option, bookwp_get_font_map)
 * must be loaded before setup.php (which calls both), and pro-mode.php
 * (defines bookwp_is_pro_mode) must be loaded before chapters.php and admin.php.
 *
 * @package book-wp
 * @version 2.0.0
 * @license GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bookwp_includes = array(
	'inc/customizer.php',      // Options helper, font map, body class — loaded first.
	'inc/pro-mode.php',        // bookwp_is_pro_mode() — needed by setup, chapters, admin.
	'inc/setup.php',           // Theme supports, enqueue, sidebar.
	'inc/chapters.php',        // Chapter query helpers.
	'inc/admin.php',           // Admin notices, legacy options page.
	'inc/structured-data.php', // JSON-LD, extensibility hooks, head cleanup.
);

foreach ( $bookwp_includes as $bookwp_file ) {
	require_once get_template_directory() . '/' . $bookwp_file;
}
