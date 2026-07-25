<?php
/**
 * Creator Dashboard automation provider.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Supply automation data to the theme.
 */
final class DPPA_Dashboard_Provider {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter(
			'dpp_automation_center_data',
			array( __CLASS__, 'provide_data' )
		);
	}

	/**
	 * Provide initial automation data.
	 *
	 * @param array<string, mixed> $data Existing dashboard data.
	 * @return array<string, mixed>
	 */
	public static function provide_data( $data ) {
		$settings = DPPA_Settings::get_settings();
		$client   = new DPPA_API_Client();

		$data['n8n_url'] = $settings['n8n_url'];

		if ( ! $client->is_configured() ) {
			return $data;
		}

		$data['connection_status'] = 'configured';

		return $data;
	}
}
