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
			self::redirect_with_notice(
				'error',
				__(
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
					'Browse the workflows published by your connected n8n discovery service.',
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

				if (link.dataset.requiresConfirmation !== "1") {
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
		DPPA_Workflow_Discovery_Provider::clear_cache();
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
			self::redirect_with_notice(
				'error',
				__(
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

		$workflow = DPPA_Workflow_Discovery_Provider::get(
			$workflow_id,
			true
		);

		if ( is_wp_error( $workflow ) ) {
			self::redirect_with_notice(
				'error',
				$workflow->get_error_message()
			);
		}

		$minimum_capability = isset( $workflow['minimum_capability'] )
			? (string) $workflow['minimum_capability']
			: 'manage_woocommerce';

		if ( ! current_user_can( $minimum_capability ) ) {
			self::redirect_with_notice(
				'error',
				__(
					'You do not have permission to run this workflow.',
					'digital-products-pro-automation'
				)
			);
		}

		if (
			empty( $workflow['active'] ) ||
			empty( $workflow['runnable'] ) ||
			empty( $workflow['supports_manual_run'] )
		) {
			self::redirect_with_notice(
				'error',
				__(
					'This workflow is not available for manual execution.',
					'digital-products-pro-automation'
				)
			);
		}

		if ( ! empty( $workflow['maintenance'] ) ) {
			self::redirect_with_notice(
				'error',
				__(
					'This workflow is temporarily unavailable due to maintenance.',
					'digital-products-pro-automation'
				)
			);
		}

		$submitted_parameters = array();

		if (
			isset( $_POST['parameters'] ) &&
			is_array( $_POST['parameters'] )
		) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Values are normalized and sanitized against the remote workflow schema below.
			$submitted_parameters = wp_unslash( $_POST['parameters'] );
		}

		$schema = DPPA_Workflow_Schema_Provider::get( $workflow_id );

		if ( is_wp_error( $schema ) ) {
			self::redirect_with_notice(
				'error',
				$schema->get_error_message()
			);
		}

		$parameter_schema = isset( $schema['parameters'] ) &&
			is_array( $schema['parameters'] )
			? $schema['parameters']
			: array();

		$parameters = DPPA_Workflow_Parameter_Schema::normalize(
			$submitted_parameters,
			$parameter_schema
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
			self::redirect_with_notice(
				'error',
				__(
					'You do not have permission to run this workflow.',
					'digital-products-pro-automation'
				)
			);
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only workflow identifier used to render the form.
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

		$workflow = DPPA_Workflow_Discovery_Provider::get( $workflow_id );

		if ( is_wp_error( $workflow ) ) {
			self::redirect_with_notice(
				'error',
				$workflow->get_error_message()
			);
		}

		$minimum_capability = isset( $workflow['minimum_capability'] )
			? (string) $workflow['minimum_capability']
			: 'manage_woocommerce';

		if ( ! current_user_can( $minimum_capability ) ) {
			self::redirect_with_notice(
				'error',
				__(
					'You do not have permission to run this workflow.',
					'digital-products-pro-automation'
				)
			);
		}

		if (
			empty( $workflow['active'] ) ||
			empty( $workflow['runnable'] ) ||
			empty( $workflow['supports_manual_run'] )
		) {
			self::redirect_with_notice(
				'error',
				__(
					'This workflow is not available for manual execution.',
					'digital-products-pro-automation'
				)
			);
		}

		if ( ! empty( $workflow['maintenance'] ) ) {
			self::redirect_with_notice(
				'error',
				__(
					'This workflow is temporarily unavailable due to maintenance.',
					'digital-products-pro-automation'
				)
			);
		}

		$schema = DPPA_Workflow_Schema_Provider::get( $workflow_id );

		$schema_error = is_wp_error( $schema )
			? $schema->get_error_message()
			: '';

		$parameter_schema = ! is_wp_error( $schema ) &&
			isset( $schema['parameters'] ) &&
			is_array( $schema['parameters'] )
			? $schema['parameters']
			: array();

		$schema_title = ! is_wp_error( $schema ) &&
			isset( $schema['title'] )
			? sanitize_text_field( (string) $schema['title'] )
			: '';

		$schema_description = ! is_wp_error( $schema ) &&
			isset( $schema['description'] )
			? sanitize_textarea_field( (string) $schema['description'] )
			: '';
		?>
		<div class="wrap dppa-run-workflow-page">
			<h1>
				<?php
				echo esc_html(
					'' !== $schema_title
						? $schema_title
						: __(
							'Run Workflow',
							'digital-products-pro-automation'
						)
				);
				?>
			</h1>

			<?php if ( '' !== $schema_description ) : ?>
				<p class="description">
					<?php echo esc_html( $schema_description ); ?>
				</p>
			<?php endif; ?>

			<?php if ( '' !== $schema_error ) : ?>
				<div class="notice notice-error inline">
					<p><?php echo esc_html( $schema_error ); ?></p>
				</div>
			<?php else : ?>
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

					<?php if ( empty( $parameter_schema ) ) : ?>
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
							<?php foreach ( $parameter_schema as $field_key => $field ) : ?>
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
			<?php endif; ?>

			<p>
				<a
					href="
					<?php
					echo esc_url(
						add_query_arg(
							array(
								'page' => 'dppa-workflows',
							),
							admin_url( 'admin.php' )
						)
					);
					?>
					"
				>
					<?php
					esc_html_e(
						'Back to workflows',
						'digital-products-pro-automation'
					);
					?>
				</a>
			</p>
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

					case 'textarea':
						self::render_textarea_field(
							$input_id,
							$input_name,
							$default_value,
							$field
						);
						break;

					case 'email':
						self::render_email_field(
							$input_id,
							$input_name,
							$default_value,
							$field
						);
						break;

					case 'url':
						self::render_url_field(
							$input_id,
							$input_name,
							$default_value,
							$field
						);
						break;

					case 'password':
						self::render_password_field(
							$input_id,
							$input_name,
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

	/**
	 * Render a textarea parameter.
	 *
	 * @param string               $input_id      Input ID.
	 * @param string               $input_name    Input name.
	 * @param mixed                $default_value Default value.
	 * @param array<string, mixed> $field         Field definition.
	 * @return void
	 */
	private static function render_textarea_field(
		$input_id,
		$input_name,
		$default_value,
		$field
	) {
		?>
		<textarea
			id="<?php echo esc_attr( $input_id ); ?>"
			name="<?php echo esc_attr( $input_name ); ?>"
			rows="<?php echo esc_attr( (string) ( $field['rows'] ?? 5 ) ); ?>"
			class="large-text"
			<?php echo ! empty( $field['required'] ) ? 'required' : ''; ?>
		><?php echo esc_textarea( (string) $default_value ); ?></textarea>
		<?php
	}

	/**
	 * Render an email parameter.
	 *
	 * @param string               $input_id      Input ID.
	 * @param string               $input_name    Input name.
	 * @param mixed                $default_value Default value.
	 * @param array<string, mixed> $field         Field definition.
	 * @return void
	 */
	private static function render_email_field(
		$input_id,
		$input_name,
		$default_value,
		$field
	) {
		?>
		<input
			type="email"
			id="<?php echo esc_attr( $input_id ); ?>"
			name="<?php echo esc_attr( $input_name ); ?>"
			value="<?php echo esc_attr( (string) $default_value ); ?>"
			class="regular-text"
			<?php echo ! empty( $field['required'] ) ? 'required' : ''; ?>
		>
		<?php
	}

	/**
	 * Render a URL parameter.
	 *
	 * @param string               $input_id      Input ID.
	 * @param string               $input_name    Input name.
	 * @param mixed                $default_value Default value.
	 * @param array<string, mixed> $field         Field definition.
	 * @return void
	 */
	private static function render_url_field(
		$input_id,
		$input_name,
		$default_value,
		$field
	) {
		?>
		<input
			type="url"
			id="<?php echo esc_attr( $input_id ); ?>"
			name="<?php echo esc_attr( $input_name ); ?>"
			value="<?php echo esc_attr( (string) $default_value ); ?>"
			class="regular-text"
			<?php echo ! empty( $field['required'] ) ? 'required' : ''; ?>
		>
		<?php
	}

	/**
	 * Render a password parameter.
	 *
	 * @param string               $input_id   Input ID.
	 * @param string               $input_name Input name.
	 * @param array<string, mixed> $field      Field definition.
	 * @return void
	 */
	private static function render_password_field(
		$input_id,
		$input_name,
		$field
	) {
		?>
		<input
			type="password"
			id="<?php echo esc_attr( $input_id ); ?>"
			name="<?php echo esc_attr( $input_name ); ?>"
			value=""
			class="regular-text"
			autocomplete="new-password"
			<?php echo ! empty( $field['required'] ) ? 'required' : ''; ?>
		>
		<?php
	}
}

