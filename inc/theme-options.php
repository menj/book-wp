<?php
/**
 * Theme Options Page (Appearance → Theme Options).
 *
 * A tabbed Settings API page that edits the `bookwp_options` array. Tabs,
 * fields, defaults and sanitizers all come from the schema in inc/options.php,
 * so the page and the Customizer always expose the same settings.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu slug. Kept identical to the 1.x options page so old bookmarks still work.
 */
const BOOKWP_OPTIONS_PAGE_SLUG = 'book-wp-options';

// ============================================================
// 1. MENU + SETTINGS REGISTRATION
// ============================================================

/**
 * Add the Theme Options submenu under Appearance.
 */
function bookwp_add_theme_options_page() {
	add_theme_page(
		esc_html__( 'Book WP Theme Options', 'book-wp' ),
		esc_html__( 'Theme Options', 'book-wp' ),
		'edit_theme_options',
		BOOKWP_OPTIONS_PAGE_SLUG,
		'bookwp_render_theme_options_page'
	);
}
add_action( 'admin_menu', 'bookwp_add_theme_options_page' );

/**
 * Register the single array setting with the Settings API.
 */
function bookwp_register_theme_options() {
	register_setting(
		'bookwp_options_group',
		BOOKWP_OPTION_NAME,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'bookwp_sanitize_options_from_form',
			'default'           => bookwp_get_option_defaults(),
		)
	);
}
add_action( 'admin_init', 'bookwp_register_theme_options' );

/**
 * Settings API sanitizer. The form posts every field (hidden inputs cover
 * unchecked boxes), so a full-array sanitize is safe here.
 *
 * @param mixed $input Raw POST data for the option.
 * @return array<string, mixed>
 */
function bookwp_sanitize_options_from_form( $input ) {
	$clean = bookwp_sanitize_options( $input );

	// Surface a confirmation message in the standard settings-errors area.
	add_settings_error(
		BOOKWP_OPTION_NAME,
		'bookwp_saved',
		esc_html__( 'Theme options saved.', 'book-wp' ),
		'success'
	);

	return $clean;
}

/**
 * Allow the Settings API to redirect back to the active tab after saving.
 *
 * @param string $location Redirect URL.
 * @return string
 */
function bookwp_keep_active_tab_on_redirect( $location ) {
	if ( ! isset( $_POST['option_page'] ) || 'bookwp_options_group' !== $_POST['option_page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- read-only redirect hint; the Settings API verifies the nonce.
		return $location;
	}
	$tab = isset( $_POST['bookwp_active_tab'] ) ? sanitize_key( wp_unslash( $_POST['bookwp_active_tab'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( $tab && array_key_exists( $tab, bookwp_get_option_tabs() ) ) {
		$location = add_query_arg( 'tab', $tab, $location );
	}
	return $location;
}
add_filter( 'wp_redirect', 'bookwp_keep_active_tab_on_redirect' );

// ============================================================
// 2. ASSETS
// ============================================================

/**
 * Enqueue the admin stylesheet and script only on the Theme Options screen.
 *
 * @param string $hook_suffix Current admin page hook.
 */
function bookwp_enqueue_theme_options_assets( $hook_suffix ) {
	if ( 'appearance_page_' . BOOKWP_OPTIONS_PAGE_SLUG !== $hook_suffix ) {
		return;
	}

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style(
		'book-wp-admin',
		get_template_directory_uri() . '/assets/css/admin.css',
		array( 'wp-color-picker' ),
		BOOKWP_VERSION
	);

	wp_enqueue_script(
		'book-wp-admin',
		get_template_directory_uri() . '/assets/js/admin.js',
		array( 'wp-color-picker' ),
		BOOKWP_VERSION,
		true
	);
	wp_localize_script(
		'book-wp-admin',
		'bookwpAdmin',
		array(
			'tabs'       => array_keys( bookwp_get_option_tabs() ),
			'defaultTab' => 'design',
			'i18n'       => array(
				'clear'   => __( 'Clear', 'book-wp' ),
				'default' => __( 'Scheme default', 'book-wp' ),
				'unsaved' => __( 'You have unsaved changes.', 'book-wp' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'bookwp_enqueue_theme_options_assets' );

// ============================================================
// 3. RENDERING
// ============================================================

/**
 * Render the Theme Options page.
 */
function bookwp_render_theme_options_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'book-wp' ) );
	}

	$tabs       = bookwp_get_option_tabs();
	$schema     = bookwp_get_option_schema();
	$options    = bookwp_get_options();
	$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'design'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only.
	if ( ! array_key_exists( $active_tab, $tabs ) ) {
		$active_tab = 'design';
	}
	?>
	<div class="wrap bookwp-options">

		<header class="bookwp-options__header">
			<div>
				<h1 class="bookwp-options__title"><?php esc_html_e( 'Theme Options', 'book-wp' ); ?></h1>
				<p class="bookwp-options__subtitle">
					<?php
					printf(
						/* translators: %s: theme version */
						esc_html__( 'Book WP %s', 'book-wp' ),
						esc_html( BOOKWP_VERSION )
					);
					?>
				</p>
			</div>
			<a class="bookwp-options__customizer-link" href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=bookwp' ) ); ?>">
				<?php esc_html_e( 'Open in Customizer for live preview', 'book-wp' ); ?>
				<span aria-hidden="true">&rarr;</span>
			</a>
		</header>

		<?php settings_errors( BOOKWP_OPTION_NAME ); ?>

		<form method="post" action="options.php" class="bookwp-options__form" id="bookwp-options-form">
			<?php settings_fields( 'bookwp_options_group' ); ?>
			<input type="hidden" name="bookwp_active_tab" id="bookwp-active-tab" value="<?php echo esc_attr( $active_tab ); ?>">

			<div class="bookwp-options__layout">

				<nav class="bookwp-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Theme option sections', 'book-wp' ); ?>">
					<?php foreach ( $tabs as $slug => $meta ) : ?>
						<a
							href="
							<?php
							echo esc_url(
								add_query_arg(
									array(
										'page' => BOOKWP_OPTIONS_PAGE_SLUG,
										'tab'  => $slug,
									),
									admin_url( 'themes.php' )
								)
							);
							?>
									"
							class="bookwp-tabs__tab<?php echo $slug === $active_tab ? ' is-active' : ''; ?>"
							id="bookwp-tab-<?php echo esc_attr( $slug ); ?>"
							role="tab"
							data-tab="<?php echo esc_attr( $slug ); ?>"
							aria-selected="<?php echo $slug === $active_tab ? 'true' : 'false'; ?>"
							aria-controls="bookwp-panel-<?php echo esc_attr( $slug ); ?>"
							tabindex="<?php echo $slug === $active_tab ? '0' : '-1'; ?>"
						>
							<?php echo esc_html( $meta['title'] ); ?>
						</a>
					<?php endforeach; ?>
				</nav>

				<div class="bookwp-panels">
					<?php foreach ( $tabs as $slug => $meta ) : ?>
						<section
							class="bookwp-panel<?php echo $slug === $active_tab ? ' is-active' : ''; ?>"
							id="bookwp-panel-<?php echo esc_attr( $slug ); ?>"
							role="tabpanel"
							aria-labelledby="bookwp-tab-<?php echo esc_attr( $slug ); ?>"
							<?php echo $slug === $active_tab ? '' : 'hidden'; ?>
						>
							<h2 class="bookwp-panel__title"><?php echo esc_html( $meta['title'] ); ?></h2>
							<p class="bookwp-panel__description"><?php echo esc_html( $meta['description'] ); ?></p>

							<?php
							foreach ( $schema as $key => $field ) {
								if ( $field['tab'] !== $slug ) {
									continue;
								}
								bookwp_render_option_field( $key, $field, $options[ $key ] );
							}
							?>
						</section>
					<?php endforeach; ?>

					<div class="bookwp-options__actions">
						<?php submit_button( esc_html__( 'Save Changes', 'book-wp' ), 'primary bookwp-button', 'submit', false ); ?>
						<span class="bookwp-options__dirty" id="bookwp-dirty" aria-live="polite"></span>
					</div>
				</div>

			</div><!-- .bookwp-options__layout -->
		</form>
	</div>
	<?php
}

/**
 * Render one field row from the schema.
 *
 * @param string               $key   Option key.
 * @param array<string, mixed> $field Schema entry.
 * @param mixed                $value Current value.
 */
function bookwp_render_option_field( $key, $field, $value ) {
	$id   = 'bookwp-field-' . $key;
	$name = BOOKWP_OPTION_NAME . '[' . $key . ']';
	$desc = $id . '-description';
	?>
	<div class="bookwp-field bookwp-field--<?php echo esc_attr( $field['type'] ); ?>">
		<div class="bookwp-field__label">
			<?php if ( 'radio' === $field['type'] ) : ?>
				<span class="bookwp-field__label-text" id="<?php echo esc_attr( $id ); ?>-legend"><?php echo esc_html( $field['label'] ); ?></span>
			<?php elseif ( 'checkbox' === $field['type'] ) : ?>
				<span class="bookwp-field__label-text"><?php echo esc_html( $field['label'] ); ?></span>
			<?php else : ?>
				<label class="bookwp-field__label-text" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
			<?php endif; ?>
			<?php if ( ! empty( $field['description'] ) ) : ?>
				<p class="bookwp-field__description" id="<?php echo esc_attr( $desc ); ?>"><?php echo esc_html( $field['description'] ); ?></p>
			<?php endif; ?>
		</div>

		<div class="bookwp-field__control">
			<?php
			switch ( $field['type'] ) {
				case 'select':
					$choices = call_user_func( $field['choices'] );
					?>
					<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class="bookwp-select" aria-describedby="<?php echo esc_attr( $desc ); ?>">
						<?php foreach ( $choices as $slug => $label ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $value, $slug ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php
					break;

				case 'radio':
					$choices = call_user_func( $field['choices'] );
					?>
					<div class="bookwp-radio-group" role="radiogroup" aria-labelledby="<?php echo esc_attr( $id ); ?>-legend" aria-describedby="<?php echo esc_attr( $desc ); ?>">
						<?php foreach ( $choices as $slug => $label ) : ?>
							<label class="bookwp-radio bookwp-radio--<?php echo esc_attr( $slug ); ?>">
								<input type="radio" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $value, $slug ); ?>>
								<span class="bookwp-radio__swatch" aria-hidden="true"></span>
								<span class="bookwp-radio__label"><?php echo esc_html( $label ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
					<?php
					break;

				case 'color':
					?>
					<input
						type="text"
						id="<?php echo esc_attr( $id ); ?>"
						name="<?php echo esc_attr( $name ); ?>"
						value="<?php echo esc_attr( $value ); ?>"
						class="bookwp-color-field"
						data-default-color=""
						aria-describedby="<?php echo esc_attr( $desc ); ?>"
						placeholder="<?php esc_attr_e( 'Scheme default', 'book-wp' ); ?>"
					>
					<?php
					break;

				case 'textarea':
					?>
					<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="4" class="bookwp-textarea" aria-describedby="<?php echo esc_attr( $desc ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
					<?php
					break;

				case 'checkbox':
					?>
					<label class="bookwp-switch" for="<?php echo esc_attr( $id ); ?>">
						<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="0">
						<input type="checkbox" role="switch" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( (int) $value, 1 ); ?> aria-describedby="<?php echo esc_attr( $desc ); ?>">
						<span class="bookwp-switch__track" aria-hidden="true"><span class="bookwp-switch__thumb"></span></span>
						<span class="screen-reader-text"><?php echo esc_html( $field['label'] ); ?></span>
						<span class="bookwp-switch__state bookwp-switch__state--on" aria-hidden="true"><?php esc_html_e( 'On', 'book-wp' ); ?></span>
						<span class="bookwp-switch__state bookwp-switch__state--off" aria-hidden="true"><?php esc_html_e( 'Off', 'book-wp' ); ?></span>
					</label>
					<?php
					break;

				case 'text':
				default:
					?>
					<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="bookwp-input regular-text" aria-describedby="<?php echo esc_attr( $desc ); ?>">
					<?php
					break;
			}
			?>
		</div>
	</div>
	<?php
}
