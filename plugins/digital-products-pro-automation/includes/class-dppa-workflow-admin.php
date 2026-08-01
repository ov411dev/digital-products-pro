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
	 * Register the workflow admin pages.
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

		add_submenu_page(
			null,
			__( 'Run Workflow', 'digital-products-pro-automation' ),
			__( 'Run Workflow', 'digital-products-pro-automation' ),
			'manage_woocommerce',
			'dppa-run-workflow',
			array( __CLASS__, 'render_run_page' )
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

		$workflow_id = isset( $_POST['workflow_id'] )
			? sanitize_text_field( wp_unslash( $_POST['workflow_id'] ) )
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

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Parameters are normalized and sanitized against the workflow schema below.
		$submitted_parameters = isset( $_POST['parameters'] ) &&
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Parameters are normalized and sanitized against the workflow schema below.
			is_array( $_POST['parameters'] )
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Parameters are normalized and sanitized against the workflow schema below.
			? wp_unslash( $_POST['parameters'] )
			: array();

		$schema = DPPA_Workflow_Parameter_Schema::get( $workflow_id );

		$parameters = DPPA_Workflow_Parameter_Schema::normalize(
			$submitted_parameters,
			$schema
		);

		$runner = new DPPA_Workflow_Runner();

		$result = $runner->run(
			$workflow_id,
			$parameters
		);

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

	/**
	 * Render the workflow parameter form.
	 *
	 * @return void
	 */
	public static function render_run_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die(
				esc_html__(
					'You do not have permission to run workflows.',
					'digital-products-pro-automation'
				)
			);
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only workflow identifier used to display the form.
		$workflow_id = isset( $_GET['workflow_id'] )
			? sanitize_text_field( wp_unslash( $_GET['workflow_id'] ) )
			: '';

		if ( '' === $workflow_id ) {
			wp_die(
				esc_html__(
					'The workflow ID is missing.',
					'digital-products-pro-automation'
				)
			);
		}
		$schema = DPPA_Workflow_Parameter_Schema::get( $workflow_id );
		?>
		<div class="wrap dppa-run-workflow-page">
			<h1>
				<?php
				esc_html_e(
					'Run Workflow',
					'digital-products-pro-automation'
				);
				?>
			</h1>

			<form
				method="post"
				action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
			>
				<input
					type="hidden"
					name="action"
					value="dppa_run_workflow"
				>

				<input
					type="hidden"
					name="workflow_id"
					value="<?php echo esc_attr( $workflow_id ); ?>"
				>

				<?php
				wp_nonce_field(
					'dppa_run_workflow_' . $workflow_id
				);
				?>

				<?php if ( empty( $schema ) ) : ?>
					<div class="notice notice-info inline">
						<p>
							<?php
							esc_html_e(
								'This workflow does not define any parameters.',
								'digital-products-pro-automation'
							);
							?>
						</p>
					</div>
				<?php else : ?>
					<table class="form-table" role="presentation">
						<?php foreach ( $schema as $field_key => $field ) : ?>
							<?php
							if ( ! is_array( $field ) ) {
								continue;
							}

							self::render_parameter_field(
								(string) $field_key,
								$field
							);
							?>
						<?php endforeach; ?>
					</table>
				<?php endif; ?>

				<?php
				submit_button(
					__(
						'Run workflow',
						'digital-products-pro-automation'
					)
				);
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render one workflow parameter field.
	 *
	 * @param string               $field_key Field key.
	 * @param array<string, mixed> $field     Field definition.
	 * @return void
	 */
	private static function render_parameter_field( $field_key, $field ) {
		$type = isset( $field['type'] )
			? sanitize_key( (string) $field['type'] )
			: 'text';

		$path = isset( $field['path'] ) && is_array( $field['path'] )
			? array_map( 'sanitize_key', $field['path'] )
			: array( sanitize_key( $field_key ) );

		$input_name = self::build_parameter_input_name( $path );
		$input_id   = 'dppa-parameter-' . implode( '-', $path );
		$label      = isset( $field['label'] )
			? (string) $field['label']
			: $field_key;
		$default    = $field['default'] ?? '';
		?>
		<tr>
			<th scope="row">
				<?php if ( 'checkbox' !== $type ) : ?>
					<label for="<?php echo esc_attr( $input_id ); ?>">
						<?php echo esc_html( $label ); ?>
					</label>
				<?php else : ?>
					<?php echo esc_html( $label ); ?>
				<?php endif; ?>
			</th>

			<td>
				<?php
				switch ( $type ) {
					case 'checkbox':
						self::render_checkbox_field(
							$input_id,
							$input_name,
							$label,
							(bool) $default
						);
						break;

					case 'number':
						self::render_number_field(
							$input_id,
							$input_name,
							$default,
							$field
						);
						break;

					case 'select':
						self::render_select_field(
							$input_id,
							$input_name,
							$default,
							$field
						);
						break;

					case 'text':
					default:
						self::render_text_field(
							$input_id,
							$input_name,
							$default,
							$field
						);
						break;
				}

				if ( ! empty( $field['description'] ) ) :
					?>
					<p class="description">
						<?php echo esc_html( (string) $field['description'] ); ?>
					</p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Build a nested parameters input name.
	 *
	 * @param string[] $path Parameter path.
	 * @return string
	 */
	private static function build_parameter_input_name( $path ) {
		$name = 'parameters';

		foreach ( $path as $segment ) {
			$name .= '[' . sanitize_key( $segment ) . ']';
		}

		return $name;
	}

	/**
	 * Render a text parameter.
	 *
	 * @param string               $input_id   Input ID.
	 * @param string               $input_name Input name.
	 * @param mixed                $default_value    Default value.
	 * @param array<string, mixed> $field      Field definition.
	 * @return void
	 */
	private static function render_text_field(
		$input_id,
		$input_name,
		$default_value,
		$field
	) {
		?>
		<input
			type="text"
			id="<?php echo esc_attr( $input_id ); ?>"
			name="<?php echo esc_attr( $input_name ); ?>"
			value="<?php echo esc_attr( (string) $default_value ); ?>"
			class="regular-text"
			<?php echo ! empty( $field['required'] ) ? 'required' : ''; ?>
		>
		<?php
	}

	/**
	 * Render a number parameter.
	 *
	 * @param string               $input_id   Input ID.
	 * @param string               $input_name Input name.
	 * @param mixed                $default_value    Default value.
	 * @param array<string, mixed> $field      Field definition.
	 * @return void
	 */
	private static function render_number_field(
		$input_id,
		$input_name,
		$default_value,
		$field
	) {
		?>
		<input
			type="number"
			id="<?php echo esc_attr( $input_id ); ?>"
			name="<?php echo esc_attr( $input_name ); ?>"
			value="<?php echo esc_attr( (string) $default ); ?>"
			min="<?php echo esc_attr( (string) ( $field['min'] ?? 0 ) ); ?>"
			<?php echo ! empty( $field['required'] ) ? 'required' : ''; ?>
		>
		<?php
	}

	/**
	 * Render a checkbox parameter.
	 *
	 * @param string $input_id   Input ID.
	 * @param string $input_name Input name.
	 * @param string $label      Field label.
	 * @param bool   $checked    Whether the field is checked.
	 * @return void
	 */
	private static function render_checkbox_field(
		$input_id,
		$input_name,
		$label,
		$checked
	) {
		?>
		<label for="<?php echo esc_attr( $input_id ); ?>">
			<input
				type="checkbox"
				id="<?php echo esc_attr( $input_id ); ?>"
				name="<?php echo esc_attr( $input_name ); ?>"
				value="1"
				<?php checked( $checked ); ?>
			>
			<?php echo esc_html( $label ); ?>
		</label>
		<?php
	}

	/**
	 * Render a select parameter.
	 *
	 * @param string               $input_id   Input ID.
	 * @param string               $input_name Input name.
	 * @param mixed                $default_value    Default value.
	 * @param array<string, mixed> $field      Field definition.
	 * @return void
	 */
	private static function render_select_field(
		$input_id,
		$input_name,
		$default_value,
		$field
	) {
		$options = isset( $field['options'] ) &&
			is_array( $field['options'] )
			? $field['options']
			: array();
		?>
		<select
			id="<?php echo esc_attr( $input_id ); ?>"
			name="<?php echo esc_attr( $input_name ); ?>"
		>
			<?php foreach ( $options as $value => $option_label ) : ?>
				<option
					value="<?php echo esc_attr( (string) $value ); ?>"
					<?php selected( (string) $default_value, (string) $value ); ?>
				>
					<?php echo esc_html( (string) $option_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}
}