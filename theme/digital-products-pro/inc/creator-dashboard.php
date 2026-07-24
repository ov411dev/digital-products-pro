<?php
/**
 * Creator dashboard data.
 *
 * @package DigitalProductsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return creator dashboard data.
 *
 * @return array<string, mixed>
 */
function dpp_get_creator_dashboard_data() {
	$default_data = array(
		'product_count'   => 0,
		'order_count'     => 0,
		'total_revenue'   => 0.0,
		'recent_products' => array(),
		'recent_orders'   => array(),
	);

	if (
		! function_exists( 'wc_get_orders' ) ||
		! function_exists( 'wc_get_products' )
	) {
		return $default_data;
	}

	$product_counts = wp_count_posts( 'product' );

	$product_count = isset( $product_counts->publish )
		? absint( $product_counts->publish )
		: 0;

	$order_results = wc_get_orders(
		array(
			'status'  => array( 'wc-processing', 'wc-completed' ),
			'limit'   => -1,
			'return'  => 'objects',
			'orderby' => 'date',
			'order'   => 'DESC',
		)
	);

	$total_revenue = 0.0;

	foreach ( $order_results as $order ) {
		if ( $order instanceof WC_Order ) {
			$total_revenue += (float) $order->get_total();
		}
	}

	$recent_orders = array_slice( $order_results, 0, 5 );

	$recent_products = wc_get_products(
		array(
			'status'  => array( 'publish', 'draft' ),
			'limit'   => 5,
			'orderby' => 'date',
			'order'   => 'DESC',
		)
	);

	return array(
		'product_count'   => $product_count,
		'order_count'     => count( $order_results ),
		'total_revenue'   => $total_revenue,
		'recent_products' => $recent_products,
		'recent_orders'   => $recent_orders,
	);
}

/**
 * Return Automation Center data.
 *
 * Plugins may provide live n8n information through the
 * dpp_automation_center_data filter.
 *
 * @return array<string, mixed>
 */
function dpp_get_automation_center_data() {
	$automation_data = array(
		'connection_status' => 'not_connected',
		'active_workflows'  => 0,
		'failed_executions' => 0,
		'last_sync'         => '',
		'workflows'         => array(),
		'n8n_url'           => '',
	);

	/**
	 * Filter Automation Center data.
	 *
	 * @param array<string, mixed> $automation_data Automation data.
	 */
	return apply_filters(
		'dpp_automation_center_data',
		$automation_data
	);
}
