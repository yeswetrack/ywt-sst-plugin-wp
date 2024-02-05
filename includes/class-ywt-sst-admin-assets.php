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
class YWT_SST_Admin_Assets {
	use YWT_SST_Singleton;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_register_script( 'vendor-jquery-validation', YWT_SST_URL . 'assets/vendors/jquery-validation/jquery.validate.min.js', array( 'jquery' ), get_ywt_sst_version(), true );

		wp_register_style( 'ywt-sst-admin', YWT_SST_URL . 'assets/css/admin-style.css', null, get_ywt_sst_version() );
		wp_register_script( 'ywt-sst-admin', YWT_SST_URL . 'assets/js/admin-javascript.js', array( 'vendor-jquery-validation' ), get_ywt_sst_version(), true );

		wp_localize_script(
			'ywt-sst-admin',
			'varGtmServerSide',
			array(
				'ajax'     => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( YWT_SST_AJAX_SECURITY ),
			)
		);
	}
}
