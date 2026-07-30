<?php
/**
 * Workflow admin page.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register and render the workflow manager.
 */
final class DPPA_Workflow_Admin {

	/**
	 * Admin page hook.
	 *
	 * @var string
	 */
	private static $page_hook = '';

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action(
			'admin_menu',
			array( __CLASS__, 'register_page' ),
			60
		);

		add_action(
			'admin_enqueue_scripts',
			array( __CLASS__, 'enqueue_assets' )
		);

		add_action(
			'admin_post_dppa_run_workflow',
			array( __CLASS__, 'handle_run_workflow' )
		);
	}

	/**
	 * Register the workflow submenu page.
	 *
	 * @return void
	 */
	public static function register_page() {
		self::$page_hook = add_submenu_page(
			'woocommerce',
			__( 'Workflows', 'digital-products-pro-automation' ),
			__( 'Workflows', 'digital-products-pro-automation' ),
			'manage_woocommerce',
			'dppa-workflows',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Render the Workflow Manager page.
	 *
	 * @return void
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die(
				esc_html__(
					'You do not have permission to access this page.',
					'digital-products-pro-automation'
				)
			);
		}

		$table = new DPPA_Workflow_Table();
		$table->prepare_items();
		?>
		<div class="wrap dppa-workflows-page">
			<h1 class="wp-heading-inline">
				<?php
				esc_html_e(
					'Workflow Manager',
					'digital-products-pro-automation'
				);
				?>
			</h1>

			<a
				class="page-title-action"
				href="<?php echo esc_url( self::get_refresh_url() ); ?>"
			>
				<?php
				esc_html_e(
					'Refresh',
					'digital-products-pro-automation'
				);
				?>
			</a>

			<hr class="wp-header-end">

			<?php settings_errors( 'dppa_workflows' ); ?>
			<?php self::render_action_notice(); ?>

			<p>
				<?php
				esc_html_e(
					'Browse the workflows available in your connected n8n instance.',
					'digital-products-pro-automation'
				);
				?>
			</p>

			<form method="get">
				<input
					type="hidden"
					name="page"
					value="dppa-workflows"
				>

				<?php
				$table->search_box(
					__( 'Search workflows', 'digital-products-pro-automation' ),
					'dppa-workflows'
				);

				$table->display();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Load Workflow Manager admin styles.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public static function enqueue_assets( $hook_suffix ) {
		if ( self::$page_hook !== $hook_suffix ) {
			return;
		}

		wp_register_style(
			'dppa-workflow-admin',
			false,
			array(),
			DPPA_VERSION
		);

		wp_enqueue_style( 'dppa-workflow-admin' );

		wp_add_inline_style(
			'dppa-workflow-admin',
			'
            .dppa-status-badge {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 600;
                line-height: 1;
                padding: 6px 10px;
            }

            .dppa-status-badge--active {
                background: #edfaef;
                color: #116329;
            }

            .dppa-status-badge--disabled {
                background: #f0f0f1;
                color: #50575e;
            }
            '
		);

		wp_register_script(
			'dppa-workflow-admin',
			'',
			array(),
			DPPA_VERSION,
			true
		);

		wp_enqueue_script( 'dppa-workflow-admin' );

		wp_add_inline_script(
			'dppa-workflow-admin',
			'
            document.addEventListener("click", function (event) {
                const link = event.target.closest(".dppa-run-workflow");

                if (!link) {
                    return;
                }

                if (!window.confirm("Run this workflow now?")) {
                    event.preventDefault();
                }
            });
            '
		);
	}

	/**
	 * Return the refresh URL.
	 *
	 * @return string
	 */
	private static function get_refresh_url() {
		return add_query_arg(
			array(
				'page'         => 'dppa-workflows',
				'dppa_refresh' => '1',
			),
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Handle a workflow run request.
	 *
	 * @return void
	 */
	public static function handle_run_workflow() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die(
				esc_html__(
					'You do not have permission to run workflows.',
					'digital-products-pro-automation'
				)
			);
		}

		$workflow_id = isset( $_GET['workflow_id'] )
			? sanitize_text_field( wp_unslash( $_GET['workflow_id'] ) )
			: '';

		if ( '' === $workflow_id ) {
			self::redirect_with_notice(
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
			self::redirect_with_notice(
				'error',
				$result->get_error_message()
			);
		}

		DPPA_Dashboard_Provider::clear_cache();

		$message = __(
			'Workflow executed successfully.',
			'digital-products-pro-automation'
		);

		if (
			is_array( $result ) &&
			isset( $result['message'] ) &&
			is_string( $result['message'] ) &&
			'' !== trim( $result['message'] )
		) {
			$message = sanitize_text_field( $result['message'] );
		}

		self::redirect_with_notice(
			'success',
			$message
		);
	}

	/**
	 * Redirect back to the Workflow Manager with a notice.
	 *
	 * @param string $type    Notice type.
	 * @param string $message Notice message.
	 * @return void
	 */
	private static function redirect_with_notice( $type, $message ) {
		$url = add_query_arg(
			array(
				'page'         => 'dppa-workflows',
				'dppa_notice'  => sanitize_key( $type ),
				'dppa_message' => $message,
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Render a workflow action notice.
	 *
	 * @return void
	 */
	private static function render_action_notice() {
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
		$type = isset( $_GET['dppa_notice'] )
			? sanitize_key( wp_unslash( $_GET['dppa_notice'] ) )
			: '';

		$message = isset( $_GET['dppa_message'] )
			? sanitize_text_field(
				wp_unslash( $_GET['dppa_message'] )
			)
			: '';
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
		if (
			'' === $message ||
			! in_array( $type, array( 'success', 'error' ), true )
		) {
			return;
		}

		$class = 'success' === $type
			? 'notice notice-success is-dismissible'
			: 'notice notice-error is-dismissible';
		?>
		<div class="<?php echo esc_attr( $class ); ?>">
			<p><?php echo esc_html( $message ); ?></p>
		</div>
		<?php
	}
}