<?php
/**
 * Execution service.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle n8n execution operations.
 */
final class DPPA_Executions {

	/**
	 * API client.
	 *
	 * @var DPPA_API_Client
	 */
	private $client;

	/**
	 * Initialize the service.
	 *
	 * @param DPPA_API_Client|null $client API client.
	 */
	public function __construct( $client = null ) {
		$this->client = $client instanceof DPPA_API_Client
			? $client
			: new DPPA_API_Client();
	}

	/**
	 * Get recent executions.
	 *
	 * @param int $limit Maximum number of executions.
	 * @return array<string, mixed>|WP_Error
	 */
	public function get_recent( $limit = 100 ) {
		return $this->client->get(
			'executions',
			array(
				'limit' => absint( $limit ),
			)
		);
	}

	/**
	 * Extract execution records from an API response.
	 *
	 * @param array<string, mixed> $response API response.
	 * @return array<int, array<string, mixed>>
	 */
	public function extract_items( $response ) {
		if ( isset( $response['data'] ) && is_array( $response['data'] ) ) {
			return $response['data'];
		}

		if ( isset( $response['items'] ) && is_array( $response['items'] ) ) {
			return $response['items'];
		}

		if ( array_is_list( $response ) ) {
			return $response;
		}

		return array();
	}
}
