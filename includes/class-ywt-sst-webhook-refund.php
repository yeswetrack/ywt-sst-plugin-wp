<?php
/**
 * Webhook Refund.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Webhook Refund.
 */
class YWT_SST_Webhook_Refund {
	use YWT_SST_Singleton;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		add_action( 'woocommerce_order_refunded', array( $this, 'woocommerce_order_refunded' ), 10, 2 );
	}

	/**
	 * Create refund
	 *
	 * @param  int $order_id Order id.
	 * @param  int $refund_id Refunded id.
	 * @return void
	 */
	public function woocommerce_order_refunded( $order_id, $refund_id ) {
		if ( ! YWT_SST_Helpers::is_enable_webhook() ) {
			return;
		}

		if ( YWT_SST_FIELD_VALUE_YES !== YWT_SST_Helpers::get_option( YWT_SST_FIELD_WEBHOOKS_REFUND ) ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! ( $order instanceof WC_Order ) ) {
			return;
		}

		$request = array(
			'event'     => 'refund',
			'ecommerce' => array(
				'transaction_id' => $refund_id,
				'value'          => YWT_SST_WC_Helpers::instance()->formatted_price( $order->get_total() ),
				'currency'       => esc_attr( $order->get_currency() ),
				'items'          => YWT_SST_WC_Helpers::instance()->get_order_data_layer_items( $order->get_items() ),
			),
			'user_data' => YWT_SST_WC_Helpers::instance()->get_order_user_data( $order ),
		);

		YWT_SST_Helpers::send_webhook_request( $request );
	}
}
