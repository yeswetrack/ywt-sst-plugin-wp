<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Define the internationalization functionality.
 */
class YWT_SST_I18n {
	use YWT_SST_Singleton;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		load_plugin_textdomain(
			'ywt-sst',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
