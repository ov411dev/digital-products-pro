<?php
/**
 * Central theme content defaults and accessors.
 *
 * @package DigitalProductsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the central theme-content defaults.
 *
 * Keeping defaults in one place makes templates reusable and allows the
 * Customizer, a future settings screen, demo imports, or plugins to override
 * content without changing template files.
 *
 * @return array<string, mixed>
 */
function dpp_content_defaults() {
	$defaults = array(
		'dpp_badge'                     => __( 'The complete digital commerce platform', 'digital-products-pro-full' ),
		'dpp_hero_title'                => __( 'Build, sell, and automate your digital product business.', 'digital-products-pro-full' ),
		'dpp_hero_text'                 => __( 'Create digital products, accept payments, automate delivery, and manage customers from one WordPress platform powered by WooCommerce, AI, and n8n.', 'digital-products-pro-full' ),
		'dpp_primary_button'            => __( 'Get Started', 'digital-products-pro-full' ),
		'dpp_primary_url'               => '#pricing',
		'dpp_secondary_button'          => __( 'Explore the Platform', 'digital-products-pro-full' ),
		'dpp_secondary_url'             => '#features',
		'dpp_dashboard_eyebrow'         => __( 'Platform overview', 'digital-products-pro-full' ),
		'dpp_dashboard_title'           => __( 'Business Command Center', 'digital-products-pro-full' ),
		'dpp_dashboard_status'          => __( 'Live', 'digital-products-pro-full' ),
		'dpp_dashboard_revenue'         => '$12,480',
		'dpp_dashboard_orders'          => '128',
		'dpp_dashboard_customers'       => '72',
		'dpp_dashboard_downloads'       => '564',
		'dpp_dashboard_revenue_delta'   => '+24%',
		'dpp_dashboard_orders_delta'    => '+18%',
		'dpp_dashboard_customers_delta' => '+11%',
		'dpp_dashboard_downloads_delta' => '+31%',
		'dpp_technology_badges'         => array(
			'WordPress',
			'WooCommerce',
			'AI',
			'n8n',
			'Docker',
			'Cloudflare',
		),
		'dpp_automation_items'          => array(
			__( 'Order delivery', 'digital-products-pro-full' ),
			__( 'Telegram notification', 'digital-products-pro-full' ),
			__( 'CRM synchronization', 'digital-products-pro-full' ),
			__( 'Follow-up email', 'digital-products-pro-full' ),
		),
		'dpp_recent_products'           => array(
			__( 'AI Prompt Pack', 'digital-products-pro-full' ),
			__( 'WordPress Theme', 'digital-products-pro-full' ),
			__( 'Automation Toolkit', 'digital-products-pro-full' ),
		),
	);

	/**
	 * Filter the complete Digital Products Pro content-default set.
	 *
	 * @param array<string, mixed> $defaults Theme content defaults.
	 */
	return apply_filters( 'dpp_content_defaults', $defaults );
}

/**
 * Return one theme content value.
 *
 * Theme mods take precedence over defaults. Arrays can be supplied by filters
 * now and by structured controls or a companion plugin later.
 *
 * @param string $key      Content key.
 * @param mixed  $fallback Optional fallback when the key is not registered.
 *
 * @return mixed
 */
function dpp_option( $key, $fallback = '' ) {
	$defaults = dpp_content_defaults();
	$default  = array_key_exists( $key, $defaults ) ? $defaults[ $key ] : $fallback;
	$value    = get_theme_mod( $key, $default );

	/**
	 * Filter one resolved Digital Products Pro content value.
	 *
	 * @param mixed  $value Resolved value.
	 * @param string $key   Content key.
	 */
	return apply_filters( 'dpp_option', $value, $key );
}

/**
 * Return dashboard values in a template-friendly structure.
 *
 * @return array<string, array<string, string>>
 */
function dpp_dashboard_data() {
	return array(
		'revenue'   => array(
			'label' => __( 'Revenue', 'digital-products-pro-full' ),
			'value' => (string) dpp_option( 'dpp_dashboard_revenue' ),
			'delta' => (string) dpp_option( 'dpp_dashboard_revenue_delta' ),
		),
		'orders'    => array(
			'label' => __( 'Orders', 'digital-products-pro-full' ),
			'value' => (string) dpp_option( 'dpp_dashboard_orders' ),
			'delta' => (string) dpp_option( 'dpp_dashboard_orders_delta' ),
		),
		'customers' => array(
			'label' => __( 'Customers', 'digital-products-pro-full' ),
			'value' => (string) dpp_option( 'dpp_dashboard_customers' ),
			'delta' => (string) dpp_option( 'dpp_dashboard_customers_delta' ),
		),
		'downloads' => array(
			'label' => __( 'Downloads', 'digital-products-pro-full' ),
			'value' => (string) dpp_option( 'dpp_dashboard_downloads' ),
			'delta' => (string) dpp_option( 'dpp_dashboard_downloads_delta' ),
		),
	);
}
