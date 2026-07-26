<?php
/**
 * Workflow service.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle n8n workflow operations.
 */
final class DPPA_Workflows {

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
	 * Get workflows.
	 *
	 * @param int $limit Maximum number of workflows.
	 * @return array<string, mixed>|WP_Error
	 */
	public function get_all( $limit = 250 ) {
		return $this->client->get(
			'workflows',
			array(
				'limit' => absint( $limit ),
			)
		);
	}

	/**
	 * Test workflow API access.
	 *
	 * @return array<string, mixed>|WP_Error
	 */
	public function test_connection() {
		return $this->get_all( 1 );
	}

	/**
	 * Extract workflow records from an API response.
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
