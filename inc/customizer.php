<?php
/**
 * Customizer Panel and Sections.
 *
 * The Customizer is a live-preview surface for the same `bookwp_options`
 * array the Theme Options page edits. Each control is bound to
 * `bookwp_options[<key>]` with type 'option', so saving in either place
 * updates the same stored values.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Customizer panel, sections, settings, and controls from the schema.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function bookwp_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'bookwp',
		array(
			'title'       => esc_html__( 'Book WP', 'book-wp' ),
			'description' => esc_html__( 'Live-preview the Book WP theme options. The same settings are available under Appearance → Theme Options.', 'book-wp' ),
			'priority'    => 30,
		)
	);

	$priority = 10;
	foreach ( bookwp_get_option_tabs() as $tab => $meta ) {
		$wp_customize->add_section(
			'bookwp_' . $tab,
			array(
				'panel'       => 'bookwp',
				'title'       => $meta['title'],
				'description' => $meta['description'],
				'priority'    => $priority,
			)
		);
		$priority += 10;
	}

	foreach ( bookwp_get_option_schema() as $key => $field ) {
		$setting_id = BOOKWP_OPTION_NAME . '[' . $key . ']';

		$wp_customize->add_setting(
			$setting_id,
			array(
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'default'           => $field['default'],
				'sanitize_callback' => $field['sanitize'],
				'transport'         => 'refresh',
			)
		);

		$control_args = array(
			'label'       => $field['label'],
			'description' => $field['description'],
			'section'     => 'bookwp_' . $field['tab'],
			'settings'    => $setting_id,
		);

		switch ( $field['type'] ) {
			case 'select':
			case 'radio':
				$control_args['type']    = $field['type'];
				$control_args['choices'] = call_user_func( $field['choices'] );
				$wp_customize->add_control( $setting_id, $control_args );
				break;

			case 'color':
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, $control_args ) );
				break;

			case 'checkbox':
			case 'textarea':
			case 'text':
			default:
				$control_args['type'] = $field['type'];
				$wp_customize->add_control( $setting_id, $control_args );
				break;
		}
	}
}
add_action( 'customize_register', 'bookwp_customize_register' );
