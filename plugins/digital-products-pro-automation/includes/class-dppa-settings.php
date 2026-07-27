<?php
/**
 * Plugin settings.
 *
 * @package DigitalProductsProAutomation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register and render automation settings.
 */
final class DPPA_Settings {

	/**
	 * Option name.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'dppa_settings';

	/**
	 * Settings page slug.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'dppa-settings';

	/**
	 * Connection status option.
	 *
	 * @var string
	 */
	const CONNECTION_STATUS_OPTION = 'dppa_connection_status';

	/**
	 * Connection test action.
	 *
	 * @var string
	 */
	const TEST_ACTION = 'dppa_test_connection';

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_filter(
			'option_page_capability_dppa_settings_group',
			array( __CLASS__, 'get_settings_capability' )
		);
		add_action(
			'admin_post_' . self::TEST_ACTION,
			array( __CLASS__, 'handle_test_connection' )
		);
	}

	/**
	 * Return the stored connection status.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_connection_status() {
		$status = get_option(
			self::CONNECTION_STATUS_OPTION,
			array()
		);

		if ( ! is_array( $status ) ) {
			$status = array();
		}

		return wp_parse_args(
			$status,
			array(
				'status'    => 'not_connected',
				'message'   => '',
				'tested_at' => '',
				'http_code' => 0,
			)
		);
	}

	/**
	 * Store the n8n connection status.
	 *
	 * @param string $status    Connection status.
	 * @param string $message   Status message.
	 * @param int    $http_code Optional HTTP status code.
	 * @return void
	 */
	public static function update_connection_status(
		$status,
		$message,
		$http_code = 0
	) {
		update_option(
			self::CONNECTION_STATUS_OPTION,
			array(
				'status'    => sanitize_key( $status ),
				'message'   => sanitize_text_field( $message ),
				'tested_at' => current_time( 'mysql' ),
				'http_code' => absint( $http_code ),
			),
			false
		);
	}

	/**
	 * Handle the Test Connection action.
	 *
	 * @return void
	 */
	public static function handle_test_connection() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die(
				esc_html__(
					'You do not have permission to test this connection.',
					'digital-products-pro-automation'
				)
			);
		}

		check_admin_referer( self::TEST_ACTION );

		$workflows = new DPPA_Workflows();
		$result    = $workflows->test_connection();

		if ( is_wp_error( $result ) ) {
			$error_data = $result->get_error_data();
			$http_code  = 0;

			if (
				is_array( $error_data ) &&
				isset( $error_data['status'] )
			) {
				$http_code = absint( $error_data['status'] );
			}

			self::update_connection_status(
				'error',
				$result->get_error_message(),
				$http_code
			);

			self::redirect_after_test( 'error' );
		}

		self::update_connection_status(
			'connected',
			__(
				'Successfully connected to the n8n API.',
				'digital-products-pro-automation'
			),
			200
		);

		delete_transient( 'dppa_dashboard_stats' );

		self::update_connection_status(
			'connected',
			__(
				'Successfully connected to the n8n API.',
				'digital-products-pro-automation'
			),
			200
		);

		self::redirect_after_test( 'success' );
	}

	/**
	 * Redirect back to the settings page after testing.
	 *
	 * @param string $result Test result.
	 * @return void
	 */
	private static function redirect_after_test( $result ) {
		$redirect_url = add_query_arg(
			array(
				'page'             => self::PAGE_SLUG,
				'dppa_test_result' => sanitize_key( $result ),
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Return the capability required to save settings.
	 *
	 * @return string
	 */
	public static function get_settings_capability() {
		return 'manage_woocommerce';
	}

	/**
	 * Register settings page.
	 *
	 * @return void
	 */
	public static function register_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Automation Settings', 'digital-products-pro-automation' ),
			__( 'Automation', 'digital-products-pro-automation' ),
			'manage_woocommerce',
			self::PAGE_SLUG,
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public static function register_settings() {
		register_setting(
			'dppa_settings_group',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
				'default'           => self::get_defaults(),
			)
		);

		add_settings_section(
			'dppa_connection_section',
			__( 'n8n connection', 'digital-products-pro-automation' ),
			array( __CLASS__, 'render_section_description' ),
			self::PAGE_SLUG
		);

		add_settings_field(
			'n8n_url',
			__( 'n8n URL', 'digital-products-pro-automation' ),
			array( __CLASS__, 'render_url_field' ),
			self::PAGE_SLUG,
			'dppa_connection_section'
		);

		add_settings_field(
			'api_key',
			__( 'API key', 'digital-products-pro-automation' ),
			array( __CLASS__, 'render_api_key_field' ),
			self::PAGE_SLUG,
			'dppa_connection_section'
		);
	}

	/**
	 * Return default settings.
	 *
	 * @return array<string, string>
	 */
	public static function get_defaults() {
		return array(
			'n8n_url' => '',
			'api_key' => '',
		);
	}

	/**
	 * Return stored settings.
	 *
	 * @return array<string, string>
	 */
	public static function get_settings() {
		$settings = get_option( self::OPTION_NAME, array() );

		return wp_parse_args(
			is_array( $settings ) ? $settings : array(),
			self::get_defaults()
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * Preserve the existing API key when the password field is left blank.
	 *
	 * @param mixed $input Submitted settings.
	 * @return array<string, string>
	 */
	public static function sanitize_settings( $input ) {
		$current = self::get_settings();
		$input   = is_array( $input ) ? $input : array();

		$n8n_url = isset( $input['n8n_url'] )
			? untrailingslashit( esc_url_raw( $input['n8n_url'] ) )
			: '';

		$api_key = isset( $input['api_key'] )
			? sanitize_text_field( wp_unslash( $input['api_key'] ) )
			: '';

		if ( '' === $api_key ) {
			$api_key = $current['api_key'];
		}

		$credentials_changed =
			$n8n_url !== $current['n8n_url'] ||
			$api_key !== $current['api_key'];

		if ( $credentials_changed ) {
			self::update_connection_status(
				'not_connected',
				__(
					'Connection settings changed. Test the connection again.',
					'digital-products-pro-automation'
				)
			);
		}

		return array(
			'n8n_url' => $n8n_url,
			'api_key' => $api_key,
		);
	}

	/**
	 * Render settings section description.
	 *
	 * @return void
	 */
	public static function render_section_description() {
		echo '<p>';
		esc_html_e(
			'Enter the public URL of your n8n instance and an n8n API key.',
			'digital-products-pro-automation'
		);
		echo '</p>';
	}

	/**
	 * Render n8n URL field.
	 *
	 * @return void
	 */
	public static function render_url_field() {
		$settings = self::get_settings();
		?>
		<input
			type="url"
			class="regular-text code"
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[n8n_url]"
			value="<?php echo esc_attr( $settings['n8n_url'] ); ?>"
			placeholder="https://n8n.example.com"
			autocomplete="url"
		/>
		<p class="description">
			<?php
			esc_html_e(
				'Do not include a trailing slash.',
				'digital-products-pro-automation'
			);
			?>
		</p>
		<?php
	}

	/**
	 * Render API-key field.
	 *
	 * @return void
	 */
	public static function render_api_key_field() {
		$settings = self::get_settings();
		?>
		<input
			type="password"
			class="regular-text"
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[api_key]"
			value=""
			placeholder="
			<?php
			echo esc_attr(
				$settings['api_key']
					? __( 'API key is saved', 'digital-products-pro-automation' )
					: __( 'Enter API key', 'digital-products-pro-automation' )
			);
			?>
			"
			autocomplete="new-password"
		/>
		<p class="description">
			<?php
			esc_html_e(
				'Leave blank to keep the currently saved API key.',
				'digital-products-pro-automation'
			);
			?>
		</p>
		<?php
	}

	/**
	 * Render settings page.
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
		?>
		<div class="wrap">
			<h1>
				<?php esc_html_e( 'Digital Products Pro Automation', 'digital-products-pro-automation' ); ?>
			</h1>
			<?php self::render_connection_notice(); ?>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'dppa_settings_group' );
				do_settings_sections( self::PAGE_SLUG );
				submit_button();
				?>
			</form>

			<hr />

			<h2>
				<?php esc_html_e( 'Connection test', 'digital-products-pro-automation' ); ?>
			</h2>

			<p>
				<?php
				esc_html_e(
					'Save your n8n URL and API key before testing the connection.',
					'digital-products-pro-automation'
				);
				?>
			</p>

			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input
					type="hidden"
					name="action"
					value="<?php echo esc_attr( self::TEST_ACTION ); ?>"
				/>

				<?php wp_nonce_field( self::TEST_ACTION ); ?>

				<?php
				submit_button(
					__( 'Test connection', 'digital-products-pro-automation' ),
					'secondary',
					'submit',
					false
				);
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the saved connection status.
	 *
	 * @return void
	 */
	private static function render_connection_notice() {
		$status = self::get_connection_status();

		if ( empty( $status['message'] ) ) {
			return;
		}

		$notice_class = 'notice notice-info';

		if ( 'connected' === $status['status'] ) {
			$notice_class = 'notice notice-success';
		} elseif ( 'error' === $status['status'] ) {
			$notice_class = 'notice notice-error';
		}
		?>
		<div class="<?php echo esc_attr( $notice_class ); ?>">
			<p>
				<strong>
					<?php echo esc_html( $status['message'] ); ?>
				</strong>

				<?php if ( ! empty( $status['tested_at'] ) ) : ?>
					<br />
					<span>
						<?php
						printf(
							/* translators: %s: connection test date and time. */
							esc_html__( 'Last tested: %s', 'digital-products-pro-automation' ),
							esc_html(
								mysql2date(
									get_option( 'date_format' ) . ' ' .
									get_option( 'time_format' ),
									$status['tested_at']
								)
							)
						);
						?>
					</span>
				<?php endif; ?>
			</p>
		</div>
		<?php
	}
}