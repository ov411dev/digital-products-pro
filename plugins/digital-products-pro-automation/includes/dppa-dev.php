<?php
/**
 * Development helpers.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add test parameters to the DPPA test workflow.
 *
 * @param array<string, mixed> $parameters  Workflow parameters.
 * @param string               $workflow_id Workflow ID.
 * @return array<string, mixed>
 */
function dppa_add_test_workflow_parameters( $parameters, $workflow_id ) {

	if ( 'nkvH7Awqu2VGGXaz' !== $workflow_id ) {
		return $parameters;
	}

	$parameters['language']   = 'en';
	$parameters['category']   = 'courses';
	$parameters['product_id'] = 123;

	$parameters['options'] = array(
		'publish' => false,
		'notify'  => true,
	);

	return $parameters;
}

add_filter(
	'dppa_workflow_parameters',
	'dppa_add_test_workflow_parameters',
	10,
	2
);
