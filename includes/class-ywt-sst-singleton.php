<?php
/**
 * Singleton.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Singleton.
 */
trait YWT_SST_Singleton {
	/**
	 * Object instance
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Gets the instance
	 *
	 * @return self
	 */
	final public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * The constructor
	 */
	final protected function __construct() {
		if ( method_exists( $this, 'init' ) ) {
			$this->init();
		}
	}
}
