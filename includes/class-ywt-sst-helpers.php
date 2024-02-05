<?php
/**
 * Helper class.
 *
 * @since      1.0.0
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Helper class.
 */
class YWT_SST_Helpers {
	/**
	 * Enable or disable data layer ecommerce.
	 *
	 * @var bool
	 */
	private static $is_enable_data_layer_ecommerce;

	/**
	 * Enable or disable data layer user data.
	 *
	 * @var bool
	 */
	private static $is_enable_data_layer_user_data;

	/**
	 * Enable or disable webhook request.
	 *
	 * @var bool
	 */
	private static $is_enable_webhook;

	/**
	 * Enable or disable cookie keeper.
	 *
	 * @var bool
	 */
	private static $is_enable_cookie_keeper;

	/**
	 * Get attr option.
	 *
	 * @param string $option The option ID.
	 *
	 * @return string|bool
	 */
	public static function get_option( $option ) {
		return get_option( $option, false );
	}

	/**
	 * Return option container placement
	 *
	 * @return string
	 */
	public static function get_option_container_placement() {
		return self::get_option( YWT_SST_FIELD_PLACEMENT );
	}

	/**
	 * Return GTM web container ID.
	 *
	 * @return string
	 */
	public static function get_gtm_container_id() {
		return self::get_option( YWT_SST_FIELD_WEB_CONTAINER_ID );
	}

	/**
	 * Return GTM web container url.
	 *
	 * @return string
	 */
	public static function get_gtm_container_url() {
		$url = self::get_option( YWT_SST_FIELD_WEB_CONTAINER_URL );

		if ( empty( $url ) ) {
			return 'https://www.googletagmanager.com';
		}

		return $url;
	}

	/**
	 * Return GTM identifier.
	 *
	 * @return string
	 */
	public static function get_gtm_container_identifier() {
		$identifier = self::get_option( YWT_SST_FIELD_WEB_IDENTIFIER );

		if ( empty( $identifier ) ) {
			return 'gtm';
		}

		return $identifier;
	}

	/**
	 * Enable or disable data layer ecommerce.
	 *
	 * @return string
	 */
	public static function is_enable_data_layer_ecommerce() {
		if ( null === static::$is_enable_data_layer_ecommerce ) {
			static::$is_enable_data_layer_ecommerce = YWT_SST_FIELD_VALUE_YES === self::get_option( YWT_SST_FIELD_DATA_LAYER_ECOMMERCE );
		}

		return static::$is_enable_data_layer_ecommerce;
	}

	/**
	 * Enable or disable data layer user data.
	 *
	 * @return string
	 */
	public static function is_enable_data_layer_user_data() {
		if ( null === static::$is_enable_data_layer_user_data ) {
			static::$is_enable_data_layer_user_data = YWT_SST_FIELD_VALUE_YES === self::get_option( YWT_SST_FIELD_DATA_LAYER_USER_DATA );
		}

		return static::$is_enable_data_layer_user_data;
	}

	/**
	 * Enable or disable webhook request.
	 *
	 * @return string
	 */
	public static function is_enable_webhook() {
		if ( null === static::$is_enable_webhook ) {
			static::$is_enable_webhook = YWT_SST_FIELD_VALUE_YES === self::get_option( YWT_SST_FIELD_WEBHOOKS_ENABLE );
		}

		return static::$is_enable_webhook;
	}

	/**
	 * Enable or disable cookie keeper.
	 *
	 * @return string
	 */
	public static function is_enable_cookie_keeper() {
		if ( null === static::$is_enable_cookie_keeper ) {
			static::$is_enable_cookie_keeper = YWT_SST_FIELD_VALUE_YES === self::get_option( YWT_SST_FIELD_COOKIE_KEEPER );
		}

		return static::$is_enable_cookie_keeper;
	}

	/**
	 * Set session.
	 *
	 * @param  mixed $name Name.
	 * @param  mixed $value Value.
	 * @return void
	 */
	public static function set_session( $name, $value ) {
		self::set_cookie(
			array(
				'name'     => $name,
				'value'    => $value,
				'secure'   => false,
				'samesite' => '',
			)
		);
	}

	/**
	 * Return session.
	 *
	 * @param  mixed $name Name.
	 * @param  mixed $default Default.
	 * @return mixed
	 */
	public static function get_session( $name, $default = null ) {
		if ( ! isset( $_COOKIE[ $name ] ) ) {
			return $default;
		}

		return filter_input( INPUT_COOKIE, $name, FILTER_DEFAULT );
	}

	/**
	 * Check exists session or not.
	 *
	 * @param  string $name Name.
	 * @param  mixed  $value Value.
	 * @return bool
	 */
	public static function exists_session( $name, $value ) {
		if ( ! isset( $_COOKIE[ $name ] ) ) {
			return false;
		}

		return $_COOKIE[ $name ] === $value;
	}

	/**
	 * Delete session.
	 *
	 * @param  string $name Name.
	 * @return void
	 */
	public static function delete_session( $name ) {
		if ( isset( $_COOKIE[ $name ] ) ) {
			self::delete_cookie( $name );
		}
	}

	/**
	 * Set cookie.
	 *
	 * @param  array $args Parameters.
	 * @return void
	 */
	public static function set_cookie( $args ) {
		$args = wp_parse_args(
			$args,
			self::get_default_cookie_options()
		);

		if ( version_compare( PHP_VERSION, '7.3.0', '>=' ) ) {
			$name  = $args['name'];
			$value = $args['value'];

			unset( $args['name'] );
			unset( $args['value'] );

			setcookie(
				$name,
				$value,
				$args,
			);
		} else {
			setcookie(
				$args['name'],
				$args['value'],
				$args['expires'],
				$args['path'],
				$args['domain'],
				$args['secure'],
				$args['httponly']
			);
		}
	}

	/**
	 * Delete cookie.
	 *
	 * @param  string $name Name.
	 * @return void
	 */
	public static function delete_cookie( $name ) {
		self::set_cookie(
			array(
				'name'    => $name,
				'value'   => '',
				'expires' => -1,
			)
		);
		unset( $_COOKIE[ $name ] );
	}

	/**
	 * Return default cookie options.
	 *
	 * @return array
	 */
	private static function get_default_cookie_options() {
		return array(
			'name'     => '',
			'value'    => '',
			'expires'  => 0,
			'path'     => '/',
			'domain'   => '.' . wp_parse_url( home_url(), PHP_URL_HOST ),
			'secure'   => true,
			'httponly' => false,
			'samesite' => 'lax',
		);
	}

	/**
	 * Delete cookie using javascript.
	 *
	 * @param  string $name Name.
	 * @return void
	 */
	public static function javascript_delete_cookie( $name ) {
		$options = self::get_default_cookie_options();
		?>
			<script>
				document.cookie = '<?php echo esc_attr( $name ); ?>=; max-age=-1; path=<?php echo esc_attr( $options['path'] ); ?>; domain=<?php echo esc_attr( $options['domain'] ); ?>;';
			</script>
		<?php
	}

	/**
	 * Sanitize bool.
	 *
	 * @param  string $value Bool.
	 * @return string
	 */
	public static function sanitize_bool( $value ) {
		return 'yes' === $value ? 'yes' : '';
	}

	/**
	 * Check if GTM plugin is enabled.
	 *
	 * @return bool
	 */
	public static function is_plugin_gtm4wp_enabled() {
		self::include_functions_plugin();

		return is_plugin_active( 'duracelltomi-google-tag-manager/duracelltomi-google-tag-manager-for-wordpress.php' );
	}

	/**
	 * Check if WooCommerce plugin is enabled.
	 *
	 * @return bool
	 */
	public static function is_plugin_wc_enabled() {
		self::include_functions_plugin();

		return is_plugin_active( 'woocommerce/woocommerce.php' );
	}

	/**
	 * Include functions plugin
	 *
	 * @return void
	 */
	private static function include_functions_plugin() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
	}

	/**
	 * Return maybe in json format or not
	 *
	 * @param  mixed $data Data.
	 * @return mixed
	 */
	public static function array_to_json( $data ) {
		return wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION );
	}

	/**
	 * Send request to webhook.
	 *
	 * @param  array $body post data.
	 * @return array|false|WP_Error The response or WP_Error on failure.
	 */
	public static function send_webhook_request( $body ) {
		$container_url = self::get_option( YWT_SST_FIELD_WEBHOOKS_CONTAINER_URL );
		if ( empty( $container_url ) ) {
			return false;
		}

		// Get all cookies from the current request
		$request_cookies = array();
		foreach ( $_COOKIE as $name => $value ) {
			$request_cookies[] = "{$name}={$value}";
		}

		// Add cookies to the request headers
		$headers = array(
			'cache-control' => 'no-cache',
			'content-type'  => 'application/json',
		);

		if ( ! empty( $request_cookies ) ) {
			$headers['Cookie'] = implode( '; ', $request_cookies );
		}

		// Send the request with updated headers
		return wp_remote_post(
			$container_url,
			array(
				'headers' => $headers,
				'body'    => wp_json_encode( $body ),
			)
		);
	}
}
