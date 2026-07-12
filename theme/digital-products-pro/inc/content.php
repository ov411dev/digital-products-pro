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
		'dpp_dashboard_trend'           => array(
			18,
			24,
			22,
			35,
			31,
			46,
			52,
			49,
			63,
			72,
			78,
			91,
		),
		'dpp_dashboard_activity'        => array(
			array(
				'time'   => '09:42',
				'title'  => __( 'Order #1203 completed', 'digital-products-pro-full' ),
				'detail' => __( 'AI Prompt Pack', 'digital-products-pro-full' ),
				'type'   => 'order',
			),
			array(
				'time'   => '09:43',
				'title'  => __( 'Telegram notification sent', 'digital-products-pro-full' ),
				'detail' => __( 'Sales channel', 'digital-products-pro-full' ),
				'type'   => 'telegram',
			),
			array(
				'time'   => '09:44',
				'title'  => __( 'Download access generated', 'digital-products-pro-full' ),
				'detail' => __( 'Secure delivery', 'digital-products-pro-full' ),
				'type'   => 'delivery',
			),
			array(
				'time'   => '09:45',
				'title'  => __( 'CRM contact updated', 'digital-products-pro-full' ),
				'detail' => __( 'Customer automation', 'digital-products-pro-full' ),
				'type'   => 'crm',
			),
		),
		'dpp_dashboard_connections'     => array(
			array(
				'label'  => 'WooCommerce',
				'status' => __( 'Connected', 'digital-products-pro-full' ),
				'health' => 100,
			),
			array(
				'label'  => 'n8n',
				'status' => __( 'Operational', 'digital-products-pro-full' ),
				'health' => 96,
			),
			array(
				'label'  => 'Telegram',
				'status' => __( 'Connected', 'digital-products-pro-full' ),
				'health' => 100,
			),
			array(
				'label'  => 'Email',
				'status' => __( 'Operational', 'digital-products-pro-full' ),
				'health' => 98,
			),
		),
		'dpp_dashboard_chart_label'     => __( 'Revenue trend', 'digital-products-pro-full' ),
		'dpp_dashboard_activity_title'  => __( 'Recent activity', 'digital-products-pro-full' ),
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

/**
 * Return normalized dashboard trend values.
 *
 * @return array<int, float>
 */
function dpp_dashboard_trend() {
	$trend = dpp_option( 'dpp_dashboard_trend', array() );

	if ( ! is_array( $trend ) ) {
		return array();
	}

	return array_map(
		static function ( $value ) {
			return (float) $value;
		},
		$trend
	);
}

/**
 * Return dashboard activity entries.
 *
 * @return array<int, array<string, mixed>>
 */
function dpp_dashboard_activity() {
	$activity = dpp_option( 'dpp_dashboard_activity', array() );

	return is_array( $activity ) ? $activity : array();
}

/**
 * Return dashboard connection-health entries.
 *
 * @return array<int, array<string, mixed>>
 */
function dpp_dashboard_connections() {
	$connections = dpp_option( 'dpp_dashboard_connections', array() );

	return is_array( $connections ) ? $connections : array();
}

/**
 * Convert dashboard trend values into SVG polyline points.
 *
 * @param array<int, float> $values Chart values.
 * @param float             $width  SVG chart width.
 * @param float             $height SVG chart height.
 *
 * @return string
 */
function dpp_dashboard_chart_points( $values, $width = 520.0, $height = 160.0 ) {
	if ( count( $values ) < 2 ) {
		return '';
	}

	$minimum = min( $values );
	$maximum = max( $values );
	$range   = max( 1.0, $maximum - $minimum );
	$step    = $width / ( count( $values ) - 1 );
	$points  = array();

	foreach ( $values as $index => $value ) {
		$x = $index * $step;
		$y = $height - ( ( $value - $minimum ) / $range * $height );

		$points[] = sprintf(
			'%1$.2f,%2$.2f',
			$x,
			$y
		);
	}

	return implode( ' ', $points );
}
