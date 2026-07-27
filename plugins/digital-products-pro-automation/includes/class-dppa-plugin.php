<?php
/**
 * Main plugin class.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin controller.
 */
final class DPPA_Plugin {

	/**
	 * Plugin instance.
	 *
	 * @var DPPA_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Return the plugin instance.
	 *
	 * @return DPPA_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin.
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->register_hooks();
	}

	/**
	 * Load plugin files.
	 *
	 * @return void
	 */
	private function load_dependencies() {
		require_once DPPA_DIR . 'includes/class-dppa-settings.php';
		require_once DPPA_DIR . 'includes/class-dppa-api-client.php';
		require_once DPPA_DIR . 'includes/class-dppa-workflows.php';
		require_once DPPA_DIR . 'includes/class-dppa-executions.php';
		require_once DPPA_DIR . 'includes/class-dppa-dashboard-provider.php';
	}

	/**
	 * Register plugin hooks.
	 *
	 * @return void
	 */
	private function register_hooks() {
		DPPA_Settings::init();
		DPPA_Dashboard_Provider::init();
	}
}
