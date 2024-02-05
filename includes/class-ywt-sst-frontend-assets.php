<?php
/**
 * Assets for admin.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Assets for admin.
 */
class YWT_SST_Frontend_Assets {
	use YWT_SST_Singleton;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! YWT_SST_WC_Helpers::instance()->is_enable_ecommerce() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_script( 'ywt-sst', YWT_SST_URL . 'assets/js/javascript.js', array( 'jquery' ), get_ywt_sst_version(), true );

		$scripts = array(
			'currency' => esc_attr( get_woocommerce_currency() ),
		);

		if ( YWT_SST_WC_Helpers::instance()->is_enable_user_data() ) {
			$scripts['user_data'] = YWT_SST_WC_Helpers::instance()->get_data_layer_user_data();
		}

		wp_localize_script( 'ywt-sst', 'varGtmServerSide', $scripts );
	}
}
