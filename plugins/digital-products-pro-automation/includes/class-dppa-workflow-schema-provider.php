<?php
/**
 * Dynamic workflow schema provider.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetch workflow parameter schemas from n8n.
 */
final class DPPA_Workflow_Schema_Provider {

	/**
	 * Get a workflow schema.
	 *
	 * @param string $workflow_id N8n workflow ID.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function get( $workflow_id ) {
		$workflow_id = sanitize_text_field( (string) $workflow_id );

		if ( '' === $workflow_id ) {
			return new WP_Error(
				'dppa_missing_workflow_id',
				__(
					'The workflow ID is missing.',
					'digital-products-pro-automation'
				)
			);
		}

		$cache_key = 'dppa_workflow_schema_' . md5( $workflow_id );
		$cached    = get_transient( $cache_key );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		$schema = self::fetch( $workflow_id );

		if ( is_wp_error( $schema ) ) {
			return $schema;
		}

		set_transient(
			$cache_key,
			$schema,
			5 * MINUTE_IN_SECONDS
		);

		return $schema;
	}

	/**
	 * Fetch a workflow schema from n8n.
	 *
	 * @param string $workflow_id Workflow ID.
	 * @return array<string, mixed>|WP_Error
	 */
	private static function fetch( $workflow_id ) {
		$url = DPPA_Settings::get_schema_webhook_url();

		if ( '' === $url ) {
			return new WP_Error(
				'dppa_missing_schema_webhook',
				__(
					'The Schema Webhook URL has not been configured.',
					'digital-products-pro-automation'
				)
			);
		}

		$response = wp_remote_post(
			$url,
			array(
				'timeout' => 15,
				'headers' => array(
					'Content-Type'         => 'application/json',
					'X-DPPA-Runner-Secret' => DPPA_Settings::get_runner_secret(),
				),
				'body'    => wp_json_encode(
					array(
						'workflow_id' => $workflow_id,
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status = wp_remote_retrieve_response_code( $response );

		if ( 200 !== $status ) {
			return new WP_Error(
				'dppa_schema_request_failed',
				sprintf(
				/* translators: %d HTTP status code. */
					__( 'Schema request failed (HTTP %d).', 'digital-products-pro-automation' ),
					$status
				)
			);
		}

		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body, true );

		if ( ! is_array( $data ) ) {
			return new WP_Error(
				'dppa_invalid_schema_response',
				__(
					'The schema response is not valid JSON.',
					'digital-products-pro-automation'
				)
			);
		}

		if ( empty( $data['success'] ) ) {
			return new WP_Error(
				'dppa_schema_error',
				isset( $data['message'] )
				? sanitize_text_field( $data['message'] )
				: __( 'The schema request failed.', 'digital-products-pro-automation' )
			);
		}

		if ( empty( $data['schema'] ) || ! is_array( $data['schema'] ) ) {
			return new WP_Error(
				'dppa_invalid_schema',
				__(
					'The workflow schema is missing.',
					'digital-products-pro-automation'
				)
			);
		}

		return $data['schema'];
	}
}
