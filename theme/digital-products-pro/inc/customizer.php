<?php
/**
 * Theme Customizer configuration.
 *
 * @package DigitalProductsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add a text-based Customizer setting and control.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @param string               $setting_id      Setting ID.
 * @param string               $section_id      Section ID.
 * @param string               $label           Control label.
 * @param mixed                $setting_default Default value.
 * @param string               $type            Control type.
 * @param string               $sanitize        Sanitization callback.
 *
 * @return void
 */
function dpp_customize_add_control(
	$wp_customize,
	$setting_id,
	$section_id,
	$label,
	$setting_default,
	$type = 'text',
	$sanitize = 'sanitize_text_field'
) {
	$wp_customize->add_setting(
		$setting_id,
		array(
			'default'           => $setting_default,
			'sanitize_callback' => $sanitize,
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		$setting_id,
		array(
			'label'   => $label,
			'section' => $section_id,
			'type'    => $type,
		)
	);
}

/**
 * Register Digital Products Pro Customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 *
 * @return void
 */
function dpp_customize_register( $wp_customize ) {
	$defaults = dpp_content_defaults();

	$wp_customize->add_panel(
		'dpp_theme_options',
		array(
			'title'       => __( 'Digital Products Pro', 'digital-products-pro-full' ),
			'description' => __( 'Edit the landing page content and platform preview.', 'digital-products-pro-full' ),
			'priority'    => 30,
		)
	);

	$wp_customize->add_section(
		'dpp_hero_options',
		array(
			'title'    => __( 'Hero', 'digital-products-pro-full' ),
			'panel'    => 'dpp_theme_options',
			'priority' => 10,
		)
	);

	$wp_customize->add_section(
		'dpp_dashboard_options',
		array(
			'title'    => __( 'Dashboard Preview', 'digital-products-pro-full' ),
			'panel'    => 'dpp_theme_options',
			'priority' => 20,
		)
	);

	$wp_customize->add_section(
		'dpp_appearance_options',
		array(
			'title'    => __( 'Appearance', 'digital-products-pro-full' ),
			'panel'    => 'dpp_theme_options',
			'priority' => 30,
		)
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_badge',
		'dpp_hero_options',
		__( 'Eyebrow text', 'digital-products-pro-full' ),
		$defaults['dpp_badge']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_hero_title',
		'dpp_hero_options',
		__( 'Hero title', 'digital-products-pro-full' ),
		$defaults['dpp_hero_title'],
		'textarea',
		'sanitize_textarea_field'
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_hero_text',
		'dpp_hero_options',
		__( 'Hero description', 'digital-products-pro-full' ),
		$defaults['dpp_hero_text'],
		'textarea',
		'sanitize_textarea_field'
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_primary_button',
		'dpp_hero_options',
		__( 'Primary button text', 'digital-products-pro-full' ),
		$defaults['dpp_primary_button']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_primary_url',
		'dpp_hero_options',
		__( 'Primary button URL', 'digital-products-pro-full' ),
		$defaults['dpp_primary_url'],
		'url',
		'esc_url_raw'
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_secondary_button',
		'dpp_hero_options',
		__( 'Secondary button text', 'digital-products-pro-full' ),
		$defaults['dpp_secondary_button']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_secondary_url',
		'dpp_hero_options',
		__( 'Secondary button URL', 'digital-products-pro-full' ),
		$defaults['dpp_secondary_url'],
		'url',
		'esc_url_raw'
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_eyebrow',
		'dpp_dashboard_options',
		__( 'Dashboard eyebrow', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_eyebrow']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_title',
		'dpp_dashboard_options',
		__( 'Dashboard title', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_title']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_status',
		'dpp_dashboard_options',
		__( 'Dashboard status', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_status']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_revenue',
		'dpp_dashboard_options',
		__( 'Revenue value', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_revenue']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_revenue_delta',
		'dpp_dashboard_options',
		__( 'Revenue change', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_revenue_delta']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_orders',
		'dpp_dashboard_options',
		__( 'Orders value', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_orders']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_orders_delta',
		'dpp_dashboard_options',
		__( 'Orders change', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_orders_delta']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_customers',
		'dpp_dashboard_options',
		__( 'Customers value', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_customers']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_customers_delta',
		'dpp_dashboard_options',
		__( 'Customers change', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_customers_delta']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_downloads',
		'dpp_dashboard_options',
		__( 'Downloads value', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_downloads']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_downloads_delta',
		'dpp_dashboard_options',
		__( 'Downloads change', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_downloads_delta']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_chart_label',
		'dpp_dashboard_options',
		__( 'Chart heading', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_chart_label']
	);

	dpp_customize_add_control(
		$wp_customize,
		'dpp_dashboard_activity_title',
		'dpp_dashboard_options',
		__( 'Activity heading', 'digital-products-pro-full' ),
		$defaults['dpp_dashboard_activity_title']
	);

	$wp_customize->add_setting(
		'dpp_dark_mode_default',
		array(
			'default'           => false,
			'sanitize_callback' => 'wp_validate_boolean',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'dpp_dark_mode_default',
		array(
			'label'   => __( 'Use dark mode by default', 'digital-products-pro-full' ),
			'section' => 'dpp_appearance_options',
			'type'    => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'dpp_customize_register' );
