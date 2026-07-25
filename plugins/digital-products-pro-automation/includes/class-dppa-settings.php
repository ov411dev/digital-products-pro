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

			<form action="options.php" method="post">
				<?php
				settings_fields( 'dppa_settings_group' );
				do_settings_sections( self::PAGE_SLUG );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}