<?php
/**
 * Main plugin class.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin controller.
 */
final class DPPA_Plugin {

	/**
	 * Plugin instance.
	 *
	 * @var DPPA_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Return the plugin instance.
	 *
	 * @return DPPA_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin.
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->register_hooks();
	}

	/**
	 * Load plugin files.
	 *
	 * @return void
	 */
	private function load_dependencies() {
		require_once DPPA_DIR . 'includes/class-dppa-settings.php';
		require_once DPPA_DIR . 'includes/class-dppa-api-client.php';
		require_once DPPA_DIR . 'includes/class-dppa-workflows.php';
		require_once DPPA_DIR . 'includes/class-dppa-executions.php';
		require_once DPPA_DIR . 'includes/class-dppa-dashboard-provider.php';
		require_once DPPA_DIR . 'includes/class-dppa-workflow-table.php';
		require_once DPPA_DIR . 'includes/class-dppa-workflow-runner.php';
		require_once DPPA_DIR . 'includes/class-dppa-workflow-admin.php';
		require_once DPPA_DIR . 'includes/class-dppa-workflow-context.php';
	}

	/**
	 * Register plugin hooks.
	 *
	 * @return void
	 */
	private function register_hooks() {
		DPPA_Settings::init();
		DPPA_Dashboard_Provider::init();
		DPPA_Workflow_Admin::init();

		add_action(
			'admin_post_dppa_run_workflow',
			array( $this, 'handle_run_workflow' )
		);
	}

	/**
	 * Handle a workflow Run action from the WordPress admin.
	 *
	 * @return void
	 */
	public function handle_run_workflow() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die(
				esc_html__(
					'You are not allowed to run workflows.',
					'digital-products-pro-automation'
				),
				esc_html__(
					'Permission denied',
					'digital-products-pro-automation'
				),
				array(
					'response' => 403,
				)
			);
		}

		$workflow_id = isset( $_GET['workflow_id'] )
			? sanitize_text_field( wp_unslash( $_GET['workflow_id'] ) )
			: '';

		if ( '' === $workflow_id ) {
			$this->redirect_after_run(
				'error',
				__(
					'The workflow ID is missing.',
					'digital-products-pro-automation'
				)
			);
		}

		check_admin_referer(
			'dppa_run_workflow_' . $workflow_id
		);

		$runner = new DPPA_Workflow_Runner();
		$result = $runner->run( $workflow_id );

		if ( is_wp_error( $result ) ) {
			$this->redirect_after_run(
				'error',
				$result->get_error_message()
			);
		}

		$message = __(
			'Workflow executed successfully.',
			'digital-products-pro-automation'
		);

		if (
			isset( $result['message'] ) &&
			is_string( $result['message'] ) &&
			'' !== trim( $result['message'] )
		) {
			$message = sanitize_text_field( $result['message'] );
		}

		$this->redirect_after_run(
			'success',
			$message
		);
	}

	/**
	 * Redirect back to the workflow manager with a notice.
	 *
	 * @param string $type    Notice type.
	 * @param string $message Notice message.
	 * @return never
	 */
	private function redirect_after_run( $type, $message ) {
		$redirect_url = add_query_arg(
			array(
				'page'         => 'dppa-workflows',
				'dppa_notice'  => sanitize_key( $type ),
				'dppa_message' => rawurlencode( $message ),
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}
}
