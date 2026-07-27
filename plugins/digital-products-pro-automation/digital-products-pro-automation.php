<?php
/**
 * Plugin Name:       Digital Products Pro Automation
 * Description:       Connects Digital Products Pro with n8n workflows and executions.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Author:            Digital Products Pro
 * Text Domain:       digital-products-pro-automation
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DPPA_VERSION', '0.1.0' );
define( 'DPPA_FILE', __FILE__ );
define( 'DPPA_DIR', plugin_dir_path( __FILE__ ) );
define( 'DPPA_URL', plugin_dir_url( __FILE__ ) );

require_once DPPA_DIR . 'includes/class-dppa-plugin.php';

/**
 * Return the main plugin instance.
 *
 * @return DPPA_Plugin
 */
function dppa_plugin() {
	return DPPA_Plugin::instance();
}

dppa_plugin();
