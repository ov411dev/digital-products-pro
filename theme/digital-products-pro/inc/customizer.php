<?php
/**
 * Customizer settings.
 *
 * @package DigitalProductsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'dpp_content',
			array(
				'title'    => __( 'Landing Page Content', 'digital-products-pro-full' ),
				'priority' => 30,
			)
		);
		$fields = array(
			'dpp_badge'            => 'Digital Product Starter Kit',
			'dpp_hero_title'       => 'Build Once. Sell Forever.',
			'dpp_hero_text'        => 'Create and sell digital downloads, templates, courses, AI prompts, and automation packs with a fast WordPress sales system.',
			'dpp_primary_button'   => 'Get the Starter Kit',
			'dpp_primary_url'      => '#pricing',
			'dpp_secondary_button' => 'See What’s Inside',
			'dpp_secondary_url'    => '#modules',
			'dpp_price'            => '$49',
			'dpp_telegram_url'     => 'https://t.me/yourchannel',
			'dpp_email'            => 'hello@example.com',
		);
		foreach ( $fields as $key => $default ) {
			$sanitize = ( str_contains( $key, 'url' ) ) ? 'esc_url_raw' : 'sanitize_text_field';
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => $default,
					'sanitize_callback' => $sanitize,
				)
			);
			$wp_customize->add_control(
				$key,
				array(
					'label'   => ucwords( str_replace( array( 'dpp_', '_' ), array( '', ' ' ), $key ) ),
					'section' => 'dpp_content',
					'type'    => 'text',
				)
			);
		}
		$wp_customize->add_setting(
			'dpp_dark_mode_default',
			array(
				'default'           => false,
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);
		$wp_customize->add_control(
			'dpp_dark_mode_default',
			array(
				'label'   => __( 'Use dark mode by default', 'digital-products-pro-full' ),
				'section' => 'dpp_content',
				'type'    => 'checkbox',
			)
		);
	}
);
