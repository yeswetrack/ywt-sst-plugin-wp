<?php
/**
 * Data Layer Event: login.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Data Layer Event: login.
 */
class YWT_SST_Event_Login {
	use YWT_SST_Singleton;

	/**
	 * Cookie name.
	 *
	 * @var string
	 */
	const CHECK_NAME = 'ywt_sst_login';

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! YWT_SST_WC_Helpers::instance()->is_enable_ecommerce() ) {
			return;
		}

		add_action( 'wp_login', array( $this, 'wp_login' ) );
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );

	}

	/**
	 * WP login hook.
	 *
	 * @return void
	 */
	public function wp_login() {
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
			'event' => 'login',
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
