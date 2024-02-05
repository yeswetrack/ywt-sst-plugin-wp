<?php
/**
 * Activate plugin.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Activate plugin.
 */
class YWT_SST_Plugin_Activate {
	use YWT_SST_Singleton;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! empty( get_option( 'ywt-sst-admin-options' ) ) ) {
			return;
		}
		$this->install();
	}

	/**
	 * First install.
	 *
	 * @return void
	 */
	private function install() {
		if ( empty( get_option( YWT_SST_FIELD_PLACEMENT ) ) ) {
			update_option( YWT_SST_FIELD_PLACEMENT, YWT_SST_FIELD_PLACEMENT_VALUE_CODE );
		}
	}
}
