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
	 * @return array
	 */
	public static function build() {
		$current_user = wp_get_current_user();

		return array(
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
	}
}
