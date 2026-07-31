<?php
/**
 * Workflow execution context.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build the execution context sent to n8n.
 */
final class DPPA_Workflow_Context {

	/**
	 * Build the workflow execution context.
	 *
	 * @param string $workflow_id N8n workflow ID.
	 * @return array<string, mixed>
	 */
	public static function build( $workflow_id ) {
		$workflow_id  = sanitize_text_field( (string) $workflow_id );
		$current_user = wp_get_current_user();

		$context = array(
			'source'    => 'wordpress',
			'trigger'   => 'manual',

			'site'      => array(
				'url'  => home_url(),
				'name' => get_bloginfo( 'name' ),
			),

			'user'      => array(
				'id'           => get_current_user_id(),
				'login'        => $current_user->user_login,
				'display_name' => $current_user->display_name,
				'email'        => $current_user->user_email,
			),

			'plugin'    => array(
				'name'    => 'Digital Products Pro Automation',
				'version' => DPPA_VERSION,
			),

			'timestamp' => gmdate( 'c' ),
		);

		/**
		 * Filter the execution context sent to n8n.
		 *
		 * @param array<string, mixed> $context     Workflow execution context.
		 * @param string               $workflow_id N8n workflow ID.
		 */
		return apply_filters(
			'dppa_workflow_context',
			$context,
			$workflow_id
		);
	}
}
