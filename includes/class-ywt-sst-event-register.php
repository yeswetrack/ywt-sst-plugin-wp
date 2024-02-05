<?php
/**
 * Data Layer Event: register.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Data Layer Event: register.
 */
class YWT_SST_Event_Register {
	use YWT_SST_Singleton;

	/**
	 * Cookie name.
	 *
	 * @var string
	 */
	const CHECK_NAME = 'ywt_sst_register';

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! YWT_SST_WC_Helpers::instance()->is_enable_ecommerce() ) {
			return;
		}

		add_action( 'user_register', array( $this, 'user_register' ) );
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
		add_action( 'login_footer', array( $this, 'wp_footer' ) );
	}

	/**
	 * WP login hook.
	 *
	 * @return void
	 */
	public function user_register() {
		YWT_SST_Helpers::set_session( self::CHECK_NAME, YWT_SST_FIELD_VALUE_YES );
	}

	/**
	 * WP footer hook.
	 *
	 * @return void
	 */
	public function wp_footer() {
		if ( ! YWT_SST_Helpers::exists_session( self::CHECK_NAME, YWT_SST_FIELD_VALUE_YES ) ) {
			return;
		}

		$data_layer = array(
			'event' => 'sign_up',
		);

		if ( YWT_SST_WC_Helpers::instance()->is_enable_user_data() ) {
			$data_layer['user_data'] = YWT_SST_WC_Helpers::instance()->get_data_layer_user_data();
		}
		?>
		<script type="text/javascript">
			dataLayer.push(<?php echo YWT_SST_Helpers::array_to_json( $data_layer ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>);
		</script>
		<?php
		YWT_SST_Helpers::javascript_delete_cookie( self::CHECK_NAME );
	}
}
