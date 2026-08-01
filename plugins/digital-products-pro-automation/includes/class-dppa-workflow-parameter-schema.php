<?php
/**
 * Workflow parameter schemas.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define and normalize workflow-specific parameters.
 */
final class DPPA_Workflow_Parameter_Schema {

	/**
	 * Get the parameter schema for a workflow.
	 *
	 * @param string $workflow_id N8n workflow ID.
	 * @return array<string, array<string, mixed>>
	 */
	public static function get( $workflow_id ) {
		$workflow_id = sanitize_text_field( (string) $workflow_id );
		$schema      = array();

		/*
		 * DPPA Test workflow.
		 */
		if ( 'nkvH7Awqu2VGGXaz' === $workflow_id ) {
			$schema = array(
				'language'   => array(
					'type'        => 'text',
					'label'       => __(
						'Language',
						'digital-products-pro-automation'
					),
					'default'     => 'en',
					'description' => __(
						'Language code used by the workflow.',
						'digital-products-pro-automation'
					),
					'required'    => true,
				),
				'category'   => array(
					'type'        => 'text',
					'label'       => __(
						'Category',
						'digital-products-pro-automation'
					),
					'default'     => 'courses',
					'description' => __(
						'Product category passed to the workflow.',
						'digital-products-pro-automation'
					),
					'required'    => true,
				),
				'product_id' => array(
					'type'        => 'number',
					'label'       => __(
						'Product ID',
						'digital-products-pro-automation'
					),
					'default'     => 0,
					'min'         => 0,
					'description' => __(
						'Optional WooCommerce product ID.',
						'digital-products-pro-automation'
					),
				),
				'publish'    => array(
					'type'    => 'checkbox',
					'path'    => array( 'options', 'publish' ),
					'label'   => __(
						'Publish',
						'digital-products-pro-automation'
					),
					'default' => false,
				),
				'notify'     => array(
					'type'    => 'checkbox',
					'path'    => array( 'options', 'notify' ),
					'label'   => __(
						'Notify',
						'digital-products-pro-automation'
					),
					'default' => false,
				),
			);
		}

		/**
		 * Filter the parameter schema for a workflow.
		 *
		 * @param array<string, array<string, mixed>> $schema      Parameter schema.
		 * @param string                              $workflow_id N8n workflow ID.
		 */
		$schema = apply_filters(
			'dppa_workflow_parameter_schema',
			$schema,
			$workflow_id
		);

		return is_array( $schema ) ? $schema : array();
	}

	/**
	 * Normalize submitted parameters using a workflow schema.
	 *
	 * @param array<string, mixed>                $submitted Submitted parameters.
	 * @param array<string, array<string, mixed>> $schema    Parameter schema.
	 * @return array<string, mixed>
	 */
	public static function normalize( $submitted, $schema ) {
		$submitted  = is_array( $submitted ) ? $submitted : array();
		$normalized = array();

		foreach ( $schema as $field_key => $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$type = isset( $field['type'] )
				? sanitize_key( (string) $field['type'] )
				: 'text';

			$path = isset( $field['path'] ) && is_array( $field['path'] )
				? array_map( 'sanitize_key', $field['path'] )
				: array( sanitize_key( (string) $field_key ) );

			$value = self::get_value_by_path(
				$submitted,
				$path
			);

			switch ( $type ) {
				case 'checkbox':
					$value = null !== $value;
					break;

				case 'number':
					$value = null !== $value
						? absint( $value )
						: absint( $field['default'] ?? 0 );
					break;

				case 'select':
					$value = sanitize_text_field(
						(string) (
							null !== $value
								? $value
								: $field['default'] ?? ''
						)
					);

					$options = isset( $field['options'] ) &&
						is_array( $field['options'] )
						? $field['options']
						: array();

					if ( ! array_key_exists( $value, $options ) ) {
						$value = sanitize_text_field(
							(string) ( $field['default'] ?? '' )
						);
					}
					break;

				case 'text':
				default:
					$value = sanitize_text_field(
						(string) (
							null !== $value
								? $value
								: $field['default'] ?? ''
						)
					);
					break;
			}

			self::set_value_by_path(
				$normalized,
				$path,
				$value
			);
		}

		return $normalized;
	}

	/**
	 * Get a nested value using its path.
	 *
	 * @param array<string, mixed> $values Values.
	 * @param string[]             $path   Value path.
	 * @return mixed
	 */
	private static function get_value_by_path( $values, $path ) {
		$current = $values;

		foreach ( $path as $segment ) {
			if (
				! is_array( $current ) ||
				! array_key_exists( $segment, $current )
			) {
				return null;
			}

			$current = $current[ $segment ];
		}

		return $current;
	}

	/**
	 * Set a nested value using its path.
	 *
	 * @param array<string, mixed> $values Values.
	 * @param string[]             $path   Value path.
	 * @param mixed                $value  Value.
	 * @return void
	 */
	private static function set_value_by_path( &$values, $path, $value ) {
		$current = &$values;

		foreach ( $path as $segment ) {
			if (
				! isset( $current[ $segment ] ) ||
				! is_array( $current[ $segment ] )
			) {
				$current[ $segment ] = array();
			}

			$current = &$current[ $segment ];
		}

		$current = $value;
	}
}
