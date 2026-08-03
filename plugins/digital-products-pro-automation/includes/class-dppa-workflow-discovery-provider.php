<?php
/**
 * Workflow discovery provider.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetch and cache discoverable workflows from n8n.
 */
final class DPPA_Workflow_Discovery_Provider {

	/**
	 * Cache key.
	 *
	 * @var string
	 */
	private const CACHE_KEY = 'dppa_discovered_workflows';

	/**
	 * Get all discoverable workflows.
	 *
	 * @return array<int, array<string, mixed>>|WP_Error
	 */
	public static function get_all() {
		$cached = get_transient( self::CACHE_KEY );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		$workflows = self::fetch();

		if ( is_wp_error( $workflows ) ) {
			return $workflows;
		}

		set_transient(
			self::CACHE_KEY,
			$workflows,
			5 * MINUTE_IN_SECONDS
		);

		return $workflows;
	}

	/**
	 * Clear the workflow discovery cache.
	 *
	 * @return void
	 */
	public static function clear_cache() {
		delete_transient( self::CACHE_KEY );
	}

	/**
	 * Fetch workflows from the n8n discovery webhook.
	 *
	 * @return array<int, array<string, mixed>>|WP_Error
	 */
	private static function fetch() {
		$url    = DPPA_Settings::get_discovery_webhook_url();
		$secret = DPPA_Settings::get_runner_secret();

		if ( '' === $url ) {
			return new WP_Error(
				'dppa_missing_discovery_webhook',
				__(
					'The Workflow Discovery Webhook URL has not been configured.',
					'digital-products-pro-automation'
				)
			);
		}

		if ( '' === $secret ) {
			return new WP_Error(
				'dppa_missing_runner_secret',
				__(
					'The Runner Secret has not been configured.',
					'digital-products-pro-automation'
				)
			);
		}

		$response = wp_remote_post(
			$url,
			array(
				'timeout'     => 15,
				'redirection' => 0,
				'headers'     => array(
					'Accept'               => 'application/json',
					'Content-Type'         => 'application/json',
					'X-DPPA-Runner-Secret' => $secret,
				),
				'body'        => wp_json_encode( array() ),
				'data_format' => 'body',
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'dppa_discovery_request_failed',
				sprintf(
					/* translators: %s: request error message. */
					__(
						'The workflow discovery request failed: %s',
						'digital-products-pro-automation'
					),
					$response->get_error_message()
				)
			);
		}

		$status_code   = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		$data          = json_decode( $response_body, true );

		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $data ) ) {
			return new WP_Error(
				'dppa_invalid_discovery_response',
				__(
					'The workflow discovery endpoint returned invalid JSON.',
					'digital-products-pro-automation'
				)
			);
		}

		if ( $status_code < 200 || $status_code >= 300 ) {
			$message = isset( $data['message'] ) && is_string( $data['message'] )
				? sanitize_text_field( $data['message'] )
				: sprintf(
					/* translators: %d: HTTP status code. */
					__(
						'The workflow discovery endpoint returned HTTP %d.',
						'digital-products-pro-automation'
					),
					$status_code
				);

			return new WP_Error(
				'dppa_discovery_http_error',
				$message
			);
		}

		if ( empty( $data['success'] ) ) {
			$message = isset( $data['message'] ) && is_string( $data['message'] )
				? sanitize_text_field( $data['message'] )
				: __(
					'Workflow discovery failed.',
					'digital-products-pro-automation'
				);

			return new WP_Error(
				'dppa_discovery_failed',
				$message
			);
		}

		if (
			! isset( $data['workflows'] ) ||
			! is_array( $data['workflows'] )
		) {
			return new WP_Error(
				'dppa_missing_discovered_workflows',
				__(
					'The workflow discovery response does not contain a workflows list.',
					'digital-products-pro-automation'
				)
			);
		}

		return self::normalize_workflows( $data['workflows'] );
	}

	/**
	 * Normalize discovered workflows.
	 *
	 * @param array<int, mixed> $workflows Raw workflows.
	 * @return array<int, array<string, mixed>>
	 */
	private static function normalize_workflows( $workflows ) {
		$normalized = array();

		foreach ( $workflows as $workflow ) {
			if ( ! is_array( $workflow ) ) {
				continue;
			}

			$id = isset( $workflow['id'] )
				? sanitize_text_field( (string) $workflow['id'] )
				: '';

			$name = isset( $workflow['name'] )
				? sanitize_text_field( (string) $workflow['name'] )
				: '';

			if ( '' === $id || '' === $name ) {
				continue;
			}

			$normalized[] = array(
				'id'          => $id,
				'name'        => $name,
				'description' => isset( $workflow['description'] )
					? sanitize_textarea_field(
						(string) $workflow['description']
					)
					: '',
				'version'     => isset( $workflow['version'] )
					? sanitize_text_field(
						(string) $workflow['version']
					)
					: '',
				'category'    => isset( $workflow['category'] )
					? sanitize_key(
						(string) $workflow['category']
					)
					: '',
				'active'      => isset( $workflow['active'] )
					? rest_sanitize_boolean( $workflow['active'] )
					: false,
				'has_schema'  => isset( $workflow['has_schema'] )
					? rest_sanitize_boolean( $workflow['has_schema'] )
					: false,
			);
		}

		return $normalized;
	}
}
