<?php
/**
 * Bootstrap file.
 *
 * @package    YWT
 */

defined( 'ABSPATH' ) || exit;

// Definitions.
define( 'YWT_SST_PATH', plugin_dir_path( __FILE__ ) );
define( 'YWT_SST_URL', plugin_dir_url( __FILE__ ) );

define( 'YWT_SST_AJAX_SECURITY', 'ywt-sst-admin__xyz' );

define( 'YWT_SST_ADMIN_SLUG', 'ywt-sst-admin-settings' );
define( 'YWT_SST_COOKIE_KEEPER_NAME', '_ywt_ck' );

define( 'YWT_SST_FIELD_VERSION', 'ywt_sst_version' );
define( 'YWT_SST_FIELD_PLACEMENT', 'ywt_sst_placement' );
define( 'YWT_SST_FIELD_WEB_CONTAINER_ID', 'ywt_sst_web_container_id' );
define( 'YWT_SST_FIELD_WEB_CONTAINER_URL', 'ywt_sst_web_container_url' );
define( 'YWT_SST_FIELD_WEB_IDENTIFIER', 'ywt_sst_web_identifier' );
define( 'YWT_SST_FIELD_COOKIE_KEEPER', 'ywt_sst_cookie_keeper' );
define( 'YWT_SST_FIELD_DATA_LAYER_ECOMMERCE', 'ywt_sst_data_layer_ecommerce' );
define( 'YWT_SST_FIELD_DATA_LAYER_USER_DATA', 'ywt_sst_data_layer_user_data' );
define( 'YWT_SST_FIELD_WEBHOOKS_ENABLE', 'ywt_sst_webhooks_enable' );
define( 'YWT_SST_FIELD_WEBHOOKS_CONTAINER_URL', 'ywt_sst_webhooks_container_url' );
define( 'YWT_SST_FIELD_WEBHOOKS_PURCHASE', 'ywt_sst_webhooks_purchase' );
define( 'YWT_SST_FIELD_WEBHOOKS_REFUND', 'ywt_sst_webhooks_refund' );

define( 'YWT_SST_FIELD_PLACEMENT_VALUE_CODE', 'code' );
define( 'YWT_SST_FIELD_PLACEMENT_VALUE_PLUGIN', 'plugin' );
define( 'YWT_SST_FIELD_PLACEMENT_VALUE_DISABLE', 'disable' );
define( 'YWT_SST_FIELD_VALUE_YES', 'yes' );

define( 'YWT_SST_ADMIN_GROUP', 'ywt-sst-admin-group' );
define( 'YWT_SST_ADMIN_GROUP_GENERAL', 'ywt-sst-admin-group-general' );
define( 'YWT_SST_ADMIN_GROUP_DATA_LAYER', 'ywt-sst-admin-group-data-layer' );
define( 'YWT_SST_ADMIN_GROUP_WEBHOOKS', 'ywt-sst-admin-group-webhooks' );

// Autoload plugin classes.
spl_autoload_register(
	function ( $class ) {
		if ( 0 === strpos( $class, 'YWT_SST' ) ) {
			$file_name = 'class-' . str_replace( '_', '-', strtolower( $class ) );
			include_once YWT_SST_PATH . 'includes' . DIRECTORY_SEPARATOR . $file_name . '.php';
		}
	}
);

// Create custom hooks.
add_action(
	'plugins_loaded',
	function () {
		do_action( 'ywt_sst' );
		if ( is_admin() ) {
			do_action( 'ywt_sst_admin' );
		} else {
			do_action( 'ywt_sst_frontend' );
		}
	},
	-1
);

/**
 * Return gtm server side version
 *
 * @return string
 */
function get_ywt_sst_version() {
	static $version;

	if ( null === $version ) {
		$plugin  = get_file_data(
			YWT_SST_PATH . 'ywt-sst.php',
			array(
				'version' => 'Version',
			),
			false
		);
		$version = $plugin['version'];
	}

	return $version;
}
