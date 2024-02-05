<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * The admin-specific functionality of the plugin.
 */
class YWT_SST_Admin_Settings {
	use YWT_SST_Singleton;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
	}

	/**
	 * Add settings menu.
	 *
	 * @since    1.0.0
	 */
	public function admin_init() {

		switch ( self::get_settings_tab() ) :
			case 'data-layer':
				$this->settings_tab_data_layer();
				break;
			case 'webhooks':
				$this->settings_tab_webhooks();
				break;
			case 'general':
			default:
				$this->settings_tab_general();
				break;
		endswitch;
	}

	/**
	 * Settings tab general.
	 *
	 * @return void
	 */
	public function settings_tab_general() {
		add_settings_section(
			YWT_SST_ADMIN_GROUP_GENERAL,
			__( 'General', 'ywt-sst' ),
			null,
			YWT_SST_ADMIN_SLUG
		);

		register_setting(
			YWT_SST_ADMIN_GROUP,
			YWT_SST_FIELD_PLACEMENT,
			array(
				'sanitize_callback' => function( $value ) {
					$allows = array(
						YWT_SST_FIELD_PLACEMENT_VALUE_CODE,
						YWT_SST_FIELD_PLACEMENT_VALUE_PLUGIN,
						YWT_SST_FIELD_PLACEMENT_VALUE_DISABLE,
					);
					return in_array( $value, $allows, true ) ? $value : YWT_SST_FIELD_PLACEMENT_VALUE_CODE;
				},
			)
		);

		$placement = YWT_SST_Helpers::get_option( YWT_SST_FIELD_PLACEMENT );
		add_settings_field(
			YWT_SST_FIELD_PLACEMENT . '-' . YWT_SST_FIELD_PLACEMENT_VALUE_CODE,
			__( 'Add web GTM script onto every page of your website', 'ywt-sst' ),
			function() use ( $placement ) {
				echo '<input
					type="radio"
					id="' . esc_attr( YWT_SST_FIELD_PLACEMENT . '-' . YWT_SST_FIELD_PLACEMENT_VALUE_CODE ) . '"
					class="js-' . esc_attr( YWT_SST_FIELD_PLACEMENT ) . '"
					name="' . esc_attr( YWT_SST_FIELD_PLACEMENT ) . '"
					' . checked( $placement, YWT_SST_FIELD_PLACEMENT_VALUE_CODE, false ) . '
					value="' . esc_attr( YWT_SST_FIELD_PLACEMENT_VALUE_CODE ) . '">';
				esc_html_e( 'Select this option if you want to embed the web GTM snippet code onto every page of your website.', 'ywt-sst' );
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_GENERAL
		);
		add_settings_field(
			YWT_SST_FIELD_PLACEMENT . '-' . YWT_SST_FIELD_PLACEMENT_VALUE_PLUGIN,
			__( 'Update existing web GTM script', 'ywt-sst' ),
			function() use ( $placement ) {
				echo '<input
					type="radio"
					id="' . esc_attr( YWT_SST_FIELD_PLACEMENT . '-' . YWT_SST_FIELD_PLACEMENT_VALUE_PLUGIN ) . '"
					class="js-' . esc_attr( YWT_SST_FIELD_PLACEMENT ) . '"
					name="' . esc_attr( YWT_SST_FIELD_PLACEMENT ) . '"
					' . checked( $placement, YWT_SST_FIELD_PLACEMENT_VALUE_PLUGIN, false ) . '
					' . ( YWT_SST_Helpers::is_plugin_gtm4wp_enabled() ? '' : 'disabled' ) . '
					value="' . esc_attr( YWT_SST_FIELD_PLACEMENT_VALUE_PLUGIN ) . '">';
				esc_html_e( 'Use this option if you require or have already inserted the web GTM container code manually or through another plugin. In this case YesWeTrack SST plugin will not add web GTM code onto your website, it will only modify the existing GTM code. This selection becomes available only if the web GTM script has been successfully found on your website.', 'ywt-sst' );
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_GENERAL
		);
		add_settings_field(
			YWT_SST_FIELD_PLACEMENT . '-' . YWT_SST_FIELD_PLACEMENT_VALUE_DISABLE,
			__( 'Disable', 'ywt-sst' ),
			function() use ( $placement ) {
				echo '<input
					type="radio"
					id="' . esc_attr( YWT_SST_FIELD_PLACEMENT . '-' . YWT_SST_FIELD_PLACEMENT_VALUE_DISABLE ) . '"
					class="js-' . esc_attr( YWT_SST_FIELD_PLACEMENT ) . '"
					name="' . esc_attr( YWT_SST_FIELD_PLACEMENT ) . '"
					' . checked( $placement, YWT_SST_FIELD_PLACEMENT_VALUE_DISABLE, false ) . '
					value="' . esc_attr( YWT_SST_FIELD_PLACEMENT_VALUE_DISABLE ) . '">';
					esc_html_e( 'Use this option if you do not want to insert web GTM snippet code onto your website.', 'ywt-sst' );
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_GENERAL
		);

		register_setting( YWT_SST_ADMIN_GROUP, YWT_SST_FIELD_WEB_CONTAINER_ID );
		add_settings_field(
			YWT_SST_FIELD_WEB_CONTAINER_ID,
			__( 'Web Google Tag Manager ID', 'ywt-sst' ),
			function() {
				echo '<input
					type="text"
					id="' . esc_attr( YWT_SST_FIELD_WEB_CONTAINER_ID ) . '"
					name="' . esc_attr( YWT_SST_FIELD_WEB_CONTAINER_ID ) . '"
					pattern="GTM-.*"
					value="' . esc_attr( YWT_SST_Helpers::get_option( YWT_SST_FIELD_WEB_CONTAINER_ID ) ) . '">';
				echo '<br>';
				esc_html_e( 'Enter the WEB Google Tag Manager ID, should be formatted as "GTM-XXXXXX".', 'ywt-sst' ); //phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_GENERAL
		);

		register_setting( YWT_SST_ADMIN_GROUP, YWT_SST_FIELD_WEB_CONTAINER_URL );
		add_settings_field(
			YWT_SST_FIELD_WEB_CONTAINER_URL,
			__( 'Server GTM container URL', 'ywt-sst' ),
			function() {
				echo '<input
					type="text"
					pattern="https://.*"
					id="' . esc_attr( YWT_SST_FIELD_WEB_CONTAINER_URL ) . '"
					name="' . esc_attr( YWT_SST_FIELD_WEB_CONTAINER_URL ) . '"
					value="' . esc_attr( YWT_SST_Helpers::get_option( YWT_SST_FIELD_WEB_CONTAINER_URL ) ) . '">';
				echo '<br>';
				;
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_GENERAL
		);

		register_setting( YWT_SST_ADMIN_GROUP, YWT_SST_FIELD_WEB_IDENTIFIER );
		add_settings_field(
			YWT_SST_FIELD_WEB_IDENTIFIER,
			__( 'YesWeTrack container ID', 'ywt-sst' ),
			function() {
				echo '<input
					type="text"
					id="' . esc_attr( YWT_SST_FIELD_WEB_IDENTIFIER ) . '"
					class="js-' . esc_attr( YWT_SST_FIELD_WEB_IDENTIFIER ) . '"
					name="' . esc_attr( YWT_SST_FIELD_WEB_IDENTIFIER ) . '"
					value="' . esc_attr( YWT_SST_Helpers::get_option( YWT_SST_FIELD_WEB_IDENTIFIER ) ) . '">';
				echo '<br>';
				printf(
					__( 'This ID is required to use features like custom loader and cookiekeeper' , 'ywt-sst'), 
				);
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_GENERAL
		);

		register_setting(
			YWT_SST_ADMIN_GROUP,
			YWT_SST_FIELD_COOKIE_KEEPER,
			array(
				'sanitize_callback' => 'YWT_SST_Helpers::sanitize_bool',
			)
		);
		add_settings_field(
			YWT_SST_FIELD_COOKIE_KEEPER,
			__( 'Cookie Keeper', 'ywt-sst' ),
			function() {
				echo '<input
					type="checkbox"
					id="' . esc_attr( YWT_SST_FIELD_COOKIE_KEEPER ) . '"
					name="' . esc_attr( YWT_SST_FIELD_COOKIE_KEEPER ) . '"
					' . checked( YWT_SST_Helpers::get_option( YWT_SST_FIELD_COOKIE_KEEPER ), 'yes', false ) . '
					value="yes">';
				echo '<br>';
				printf(
					__( 'Cookie Keeper is used to prolong cookie lifetime in Safari and other browsers with ITP.', 'ywt-sst' ), 
				);
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_GENERAL
		);
	}

	/**
	 * Settings tab data layer.
	 *
	 * @return void
	 */
	public function settings_tab_data_layer() {
		add_settings_section(
			YWT_SST_ADMIN_GROUP_DATA_LAYER,
			__( 'Data Layer', 'ywt-sst' ),
			null,
			YWT_SST_ADMIN_SLUG
		);

		register_setting(
			YWT_SST_ADMIN_GROUP,
			YWT_SST_FIELD_DATA_LAYER_ECOMMERCE,
			array(
				'sanitize_callback' => 'YWT_SST_Helpers::sanitize_bool',
			)
		);
		add_settings_field(
			YWT_SST_FIELD_DATA_LAYER_ECOMMERCE,
			__( 'Add ecommerce Data Layer events', 'ywt-sst' ),
			function() {
				echo '<input
					type="checkbox"
					id="' . esc_attr( YWT_SST_FIELD_DATA_LAYER_ECOMMERCE ) . '"
					name="' . esc_attr( YWT_SST_FIELD_DATA_LAYER_ECOMMERCE ) . '"
					' . checked( YWT_SST_Helpers::get_option( YWT_SST_FIELD_DATA_LAYER_ECOMMERCE ), 'yes', false ) . '
					value="yes">';
				echo '<br>';
				esc_html_e( 'This option only works with Woocommerce shops. Adds basic events and their data: Login, SignUp, ViewItem, AddToCart, BeginCheckout, Purchase.', 'ywt-sst' );
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_DATA_LAYER
		);

		register_setting(
			YWT_SST_ADMIN_GROUP,
			YWT_SST_FIELD_DATA_LAYER_USER_DATA,
			array(
				'sanitize_callback' => 'YWT_SST_Helpers::sanitize_bool',
			)
		);
		add_settings_field(
			YWT_SST_FIELD_DATA_LAYER_USER_DATA,
			__( 'Add user data to Data Layer events', 'ywt-sst' ),
			function() {
				echo '<input
					type="checkbox"
					id="' . esc_attr( YWT_SST_FIELD_DATA_LAYER_USER_DATA ) . '"
					name="' . esc_attr( YWT_SST_FIELD_DATA_LAYER_USER_DATA ) . '"
					' . checked( YWT_SST_Helpers::get_option( YWT_SST_FIELD_DATA_LAYER_USER_DATA ), 'yes', false ) . '
					value="yes">';
				echo '<br>';
				esc_html_e( 'All events for authorised users will have their personal details (name, surname, email, etc.) available. Their billing details will be available on the purchase event.', 'ywt-sst' );
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_DATA_LAYER
		);
	}

	/**
	 * Settings tab webhooks
	 *
	 * @return void
	 */
	public function settings_tab_webhooks() {
		add_settings_section(
			YWT_SST_ADMIN_GROUP_WEBHOOKS,
			__( 'Webhooks', 'ywt-sst' ),
			null,
			YWT_SST_ADMIN_SLUG
		);

		register_setting(
			YWT_SST_ADMIN_GROUP,
			YWT_SST_FIELD_WEBHOOKS_ENABLE,
			array(
				'sanitize_callback' => 'YWT_SST_Helpers::sanitize_bool',
			)
		);
		add_settings_field(
			YWT_SST_FIELD_WEBHOOKS_ENABLE,
			__( 'Send webhooks to server GTM container', 'ywt-sst' ),
			function() {
				echo '<input
					type="checkbox"
					id="' . esc_attr( YWT_SST_FIELD_WEBHOOKS_ENABLE ) . '"
					name="' . esc_attr( YWT_SST_FIELD_WEBHOOKS_ENABLE ) . '"
					' . checked( YWT_SST_Helpers::get_option( YWT_SST_FIELD_WEBHOOKS_ENABLE ), 'yes', false ) . '
					value="yes">';
				echo '<br>';
				printf( __( 'This option will allow webhooks to be sent to your server GTM container.', 'ywt-sst' ), ); // phpcs:ignore
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_WEBHOOKS
		);

		register_setting( YWT_SST_ADMIN_GROUP, YWT_SST_FIELD_WEBHOOKS_CONTAINER_URL );
		add_settings_field(
			YWT_SST_FIELD_WEBHOOKS_CONTAINER_URL,
			__( 'Server GTM container URL', 'ywt-sst' ),
			function() {
				echo '<input
					type="text"
					id="' . esc_attr( YWT_SST_FIELD_WEBHOOKS_CONTAINER_URL ) . '"
					name="' . esc_attr( YWT_SST_FIELD_WEBHOOKS_CONTAINER_URL ) . '"
					value="' . esc_attr( YWT_SST_Helpers::get_option( YWT_SST_FIELD_WEBHOOKS_CONTAINER_URL ) ) . '">';
				echo '<br>';
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_WEBHOOKS
		);

		register_setting(
			YWT_SST_ADMIN_GROUP,
			YWT_SST_FIELD_WEBHOOKS_PURCHASE,
			array(
				'sanitize_callback' => 'YWT_SST_Helpers::sanitize_bool',
			)
		);
		add_settings_field(
			YWT_SST_FIELD_WEBHOOKS_PURCHASE,
			__( 'Purchase webhook', 'ywt-sst' ),
			function() {
				echo '<input
					type="checkbox"
					id="' . esc_attr( YWT_SST_FIELD_WEBHOOKS_PURCHASE ) . '"
					name="' . esc_attr( YWT_SST_FIELD_WEBHOOKS_PURCHASE ) . '"
					' . checked( YWT_SST_Helpers::get_option( YWT_SST_FIELD_WEBHOOKS_PURCHASE ), 'yes', false ) . '
					value="yes">';
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_WEBHOOKS
		);

		register_setting(
			YWT_SST_ADMIN_GROUP,
			YWT_SST_FIELD_WEBHOOKS_REFUND,
			array(
				'sanitize_callback' => 'YWT_SST_Helpers::sanitize_bool',
			)
		);
		add_settings_field(
			YWT_SST_FIELD_WEBHOOKS_REFUND,
			__( 'Refund webhook', 'ywt-sst' ),
			function() {
				echo '<input
					type="checkbox"
					id="' . esc_attr( YWT_SST_FIELD_WEBHOOKS_REFUND ) . '"
					name="' . esc_attr( YWT_SST_FIELD_WEBHOOKS_REFUND ) . '"
					' . checked( YWT_SST_Helpers::get_option( YWT_SST_FIELD_WEBHOOKS_REFUND ), 'yes', false ) . '
					value="yes">';
			},
			YWT_SST_ADMIN_SLUG,
			YWT_SST_ADMIN_GROUP_WEBHOOKS
		);
	}

	/**
	 * Add settings menu.
	 *
	 * @since    1.0.0
	 */
	public function admin_menu() {
		add_options_page(
			__( 'YesWeTrack SST', 'ywt-sst' ),
			__( 'YesWeTrack SST', 'ywt-sst' ),
			'manage_options',
			YWT_SST_ADMIN_SLUG,
			function() {
				wp_enqueue_style( 'ywt-sst-admin' );
				wp_enqueue_script( 'ywt-sst-admin' );

				load_template( YWT_SST_PATH . 'templates/class-ywt-sst-admin.php', false );
			},
			27
		);
	}

	/**
	 * Add plugin links.
	 *
	 * @param array  $links Links.
	 * @param string $file File.
	 *
	 * @return mixed
	 */
	public function plugin_action_links( $links, $file ) {
		if ( strpos( $file, '/ywt-sst.php' ) === false ) {
			return $links;
		}

		$settings_link = '<a href="' . menu_page_url( YWT_SST_ADMIN_SLUG, false ) . '">' . esc_html( __( 'Settings' ) ) . '</a>';

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Return settings tab.
	 *
	 * @return string
	 */
	public static function get_settings_tab() {
		$tab = filter_input( INPUT_GET, 'tab', FILTER_DEFAULT );
		if ( ! empty( $tab ) ) {
			return $tab;
		}
		$tab = filter_input( INPUT_POST, 'tab', FILTER_DEFAULT );
		if ( ! empty( $tab ) ) {
			return $tab;
		}
		return 'general';
	}
}
