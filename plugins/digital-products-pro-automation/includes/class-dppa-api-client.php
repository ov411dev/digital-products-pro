<?php
/**
 * N8n API client.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Make authenticated requests to the n8n API.
 */
final class DPPA_API_Client {

	/**
	 * N8n base URL.
	 *
	 * @var string
	 */
	private $base_url;

	/**
	 * API key.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Construct the client.
	 */
	public function __construct() {
		$settings       = DPPA_Settings::get_settings();
		$this->base_url = $settings['n8n_url'];
		$this->api_key  = $settings['api_key'];
	}

	/**
	 * Determine whether credentials are configured.
	 *
	 * @return bool
	 */
	public function is_configured() {
		return '' !== $this->base_url && '' !== $this->api_key;
	}

	/**
	 * Make an authenticated GET request.
	 *
	 * @param string               $endpoint API endpoint.
	 * @param array<string, mixed> $query     Query arguments.
	 * @return array<string, mixed>|WP_Error
	 */
	public function get( $endpoint, $query = array() ) {
		if ( ! $this->is_configured() ) {
			return new WP_Error(
				'dppa_not_configured',
				__( 'The n8n connection is not configured.', 'digital-products-pro-automation' )
			);
		}

		$url = $this->base_url . '/api/v1/' . ltrim( $endpoint, '/' );

		if ( ! empty( $query ) ) {
			$url = add_query_arg( $query, $url );
		}

		$response = wp_safe_remote_get(
			$url,
			array(
				'timeout' => 12,
				'headers' => array(
					'Accept'        => 'application/json',
					'X-N8N-API-KEY' => $this->api_key,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = json_decode(
			wp_remote_retrieve_body( $response ),
			true
		);

		if ( $status_code < 200 || $status_code >= 300 ) {
			return new WP_Error(
				'dppa_n8n_request_failed',
				sprintf(
					/* translators: %d: HTTP status code. */
					__( 'The n8n API returned HTTP %d.', 'digital-products-pro-automation' ),
					$status_code
				),
				array(
					'status' => $status_code,
					'body'   => is_array( $body ) ? $body : array(),
				)
			);
		}

		return is_array( $body ) ? $body : array();
	}
}
