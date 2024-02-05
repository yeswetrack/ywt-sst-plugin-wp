<?php
/**
 * Data Layer Event: view_cart.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Data Layer Event: view_cart.
 */
class YWT_SST_Event_ViewCart {
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

		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
	}

	/**
	 * WP footer hook.
	 *
	 * @return void
	 */
	public function wp_footer() {
		if ( ! is_cart() ) {
			return;
		}

		$cart = WC()->cart->get_cart();
		if ( empty( $cart ) ) {
			return;
		}

		$data_layer = array(
			'event'         => 'view_cart',
			'cart_quantity' => count( $cart ),
			'cart_total'    => YWT_SST_WC_Helpers::instance()->formatted_price(
				YWT_SST_WC_Helpers::instance()->get_cart_total()
			),
			'ecommerce'     => array(
				'currency' => esc_attr( get_woocommerce_currency() ),
				'items'    => YWT_SST_WC_Helpers::instance()->get_cart_data_layer_items( $cart ),
			),
		);

		if ( YWT_SST_WC_Helpers::instance()->is_enable_user_data() ) {
			$data_layer['user_data'] = YWT_SST_WC_Helpers::instance()->get_data_layer_user_data();
		}
		?>
		<script type="text/javascript">
			dataLayer.push(<?php echo YWT_SST_Helpers::array_to_json( $data_layer ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>);
		</script>
		<?php
	}
}
