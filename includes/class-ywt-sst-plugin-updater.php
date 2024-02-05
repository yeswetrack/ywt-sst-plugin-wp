<?php

require dirname(__FILE__) . '/../plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/**
 * plugin updater
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * plugin updater.
 */
class YWT_SST_Plugin_Updater {
	use YWT_SST_Singleton;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		$this->updateChecker();
	}

	/**
	 * Check for updates
	 *
	 * @return void
	 */
	private function updateChecker() {
		$updateChecker = PucFactory::buildUpdateChecker(
			'https://github.com/yeswetrack/ywt-sst-plugin-wp',
			__FILE__,
			'yeswetrack-sst'
		);
		
		$updateChecker->setBranch('main');
	}
}
