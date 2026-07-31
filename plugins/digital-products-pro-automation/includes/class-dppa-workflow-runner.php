<?php
/**
 * Workflow runner.
 *
 * Sends workflow execution requests to the configured n8n Runner Gateway.
 *
 * @package DigitalProductsProAutomation
 */

defined( 'ABSPATH' ) || exit;

/**
 * Runs n8n workflows through the DPPA Runner Gateway.
 */
final class DPPA_Workflow_Runner {

	/**
	 * Header used to authenticate with the Runner Gateway.
	 *
	 * This must match the header name configured in the n8n
	 * Header Auth credential.
	 */
	private const AUTH_HEADER = 'X-DPPA-RUNNER-SECRET';

	/**
	 * Run a workflow.
	 *
	 * @param string               $workflow_id Workflow ID.
	 * @param array<string, mixed> $parameters  Workflow parameters.
	 * @return array<string, mixed>|WP_Error
	 */
	public function run( $workflow_id, $parameters = array() ) {
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

		$webhook_url = DPPA_Settings::get_runner_webhook_url();
		$secret      = DPPA_Settings::get_runner_secret();

		if ( '' === $webhook_url ) {
			return new WP_Error(
				'dppa_missing_runner_url',
				__(
					'The Runner Webhook URL is not configured.',
					'digital-products-pro-automation'
				)
			);
		}

		if ( '' === $secret ) {
			return new WP_Error(
				'dppa_missing_runner_secret',
				__(
					'The Runner Secret is not configured.',
					'digital-products-pro-automation'
				)
			);
		}

		$request_body = array(
			'workflow_id' => $workflow_id,
			'context'     => DPPA_Workflow_Context::build( $workflow_id ),
			'parameters'  => DPPA_Workflow_Parameters::build( $workflow_id, $parameters ),
		);

		$response = wp_remote_post(
			$webhook_url,
			array(
				'timeout'     => 30,
				'redirection' => 0,
				'headers'     => array(
					'Accept'          => 'application/json',
					'Content-Type'    => 'application/json',
					self::AUTH_HEADER => $secret,
				),
				'body'        => wp_json_encode( $request_body ),
				'data_format' => 'body',
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'dppa_runner_request_failed',
				sprintf(
					/* translators: %s: request error message. */
					__(
						'The Runner Gateway request failed: %s',
						'digital-products-pro-automation'
					),
					$response->get_error_message()
				)
			);
		}

		$status_code   = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		$decoded = json_decode( $response_body, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			return new WP_Error(
				'dppa_invalid_runner_response',
				sprintf(
					/* translators: %d: HTTP status code. */
					__(
						'The Runner Gateway returned an invalid JSON response with HTTP status %d.',
						'digital-products-pro-automation'
					),
					$status_code
				),
				array(
					'status_code'   => $status_code,
					'response_body' => $response_body,
				)
			);
		}

		if ( $status_code < 200 || $status_code >= 300 ) {
			$message = $this->get_response_message(
				$decoded,
				__(
					'The Runner Gateway returned an error.',
					'digital-products-pro-automation'
				)
			);

			return new WP_Error(
				'dppa_runner_http_error',
				$message,
				array(
					'status_code' => $status_code,
					'response'    => $decoded,
				)
			);
		}

		if (
			isset( $decoded['success'] ) &&
			false === rest_sanitize_boolean( $decoded['success'] )
		) {
			return new WP_Error(
				'dppa_workflow_execution_failed',
				$this->get_response_message(
					$decoded,
					__(
						'The workflow could not be executed.',
						'digital-products-pro-automation'
					)
				),
				array(
					'status_code' => $status_code,
					'response'    => $decoded,
				)
			);
		}

		return $decoded;
	}

	/**
	 * Extract a useful message from a Runner Gateway response.
	 *
	 * @param mixed  $response Response data.
	 * @param string $fallback Fallback message.
	 * @return string
	 */
	private function get_response_message( $response, $fallback ) {
		if ( ! is_array( $response ) ) {
			return $fallback;
		}

		if (
			isset( $response['message'] ) &&
			is_string( $response['message'] ) &&
			'' !== trim( $response['message'] )
		) {
			return sanitize_text_field( $response['message'] );
		}

		if (
			isset( $response['error'] ) &&
			is_string( $response['error'] ) &&
			'' !== trim( $response['error'] )
		) {
			return sanitize_text_field( $response['error'] );
		}

		return $fallback;
	}
}
