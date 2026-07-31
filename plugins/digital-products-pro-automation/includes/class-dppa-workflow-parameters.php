<?php
/**
 * Workflow execution parameters.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build and sanitize parameters sent to n8n workflows.
 */
final class DPPA_Workflow_Parameters {

	/**
	 * Build workflow parameters.
	 *
	 * @param string               $workflow_id Workflow ID.
	 * @param array<string, mixed> $parameters  Initial parameters.
	 * @return array<string, mixed>
	 */
	public static function build( $workflow_id, $parameters = array() ) {
		$workflow_id = sanitize_text_field( (string) $workflow_id );
		$parameters  = is_array( $parameters ) ? $parameters : array();
		$parameters  = self::sanitize( $parameters );

		/**
		 * Filter parameters sent to an n8n workflow.
		 *
		 * @param array<string, mixed> $parameters  Workflow parameters.
		 * @param string               $workflow_id Workflow ID.
		 */
		$parameters = apply_filters(
			'dppa_workflow_parameters',
			$parameters,
			$workflow_id
		);

		return is_array( $parameters )
			? self::sanitize( $parameters )
			: array();
	}

	/**
	 * Recursively sanitize workflow parameters.
	 *
	 * @param array<string|int, mixed> $parameters Parameters to sanitize.
	 * @return array<string|int, mixed>
	 */
	private static function sanitize( $parameters ) {
		$sanitized = array();

		foreach ( $parameters as $key => $value ) {
			$sanitized_key = is_int( $key )
				? $key
				: sanitize_key( (string) $key );

			if ( is_array( $value ) ) {
				$sanitized[ $sanitized_key ] = self::sanitize( $value );
				continue;
			}

			if ( is_bool( $value ) || is_int( $value ) || is_float( $value ) ) {
				$sanitized[ $sanitized_key ] = $value;
				continue;
			}

			if ( null === $value ) {
				$sanitized[ $sanitized_key ] = null;
				continue;
			}

			$sanitized[ $sanitized_key ] = sanitize_text_field(
				(string) $value
			);
		}

		return $sanitized;
	}
}
