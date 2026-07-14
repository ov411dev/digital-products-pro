<?php
/**
 * WooCommerce integration.
 *
 * @package DigitalProductsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return whether WooCommerce is active.
 *
 * @return bool
 */
function dpp_is_woocommerce_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * Remove the default WooCommerce sidebar.
 *
 * The product experience will use a full-width layout. Filters can be added
 * later through blocks or a dedicated shop sidebar.
 *
 * @return void
 */
function dpp_woocommerce_remove_sidebar() {
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action( 'wp', 'dpp_woocommerce_remove_sidebar' );
