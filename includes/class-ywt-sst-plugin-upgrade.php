<?php
/**
 * Upgrade plugin.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Upgrade plugin.
 */
class YWT_SST_Plugin_Upgrade {
	use YWT_SST_Singleton;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		$this->upgrade_1_0_0();
	}

	/**
	 * Upgrade to version 1.0.0.
	 *
	 * @return void
	 */
	private function upgrade_1_0_0() {
		if ( version_compare( get_option( YWT_SST_FIELD_VERSION, '0.0.1' ), '1.0.0', '>=' ) ) {
			return;
		}

		$options = get_option( 'ywt-sst-admin-options' );
		if ( ! empty( $options['ywt-sst-placement'] ) ) {
			if ( 'ywt-sst-placement-plugin' === $options['ywt-sst-placement'] ) {
				update_option( YWT_SST_FIELD_PLACEMENT, YWT_SST_FIELD_PLACEMENT_VALUE_PLUGIN );
			}
			if ( 'ywt-sst-placement-code' === $options['ywt-sst-placement'] ) {
				update_option( YWT_SST_FIELD_PLACEMENT, YWT_SST_FIELD_PLACEMENT_VALUE_CODE );
			}
		}

		if ( ! empty( $options['ywt-sst-server-container-url'] ) ) {
			update_option( YWT_SST_FIELD_WEB_CONTAINER_URL, $options['ywt-sst-server-container-url'] );
		}

		if ( ! empty( $options['ywt-sst-web-container-id'] ) ) {
			update_option( YWT_SST_FIELD_WEB_CONTAINER_ID, $options['ywt-sst-web-container-id'] );
		}

		if ( ! empty( $options['ywt-sst-identifier'] ) ) {
			update_option( YWT_SST_FIELD_WEB_IDENTIFIER, $options['ywt-sst-identifier'] );
		}

		if ( ! empty( $options ) ) {
			delete_option( 'ywt-sst-admin-options' );
		}

		update_option( YWT_SST_FIELD_VERSION, '1.0.0', false );
	}
}
