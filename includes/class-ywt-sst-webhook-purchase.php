<?php
/**
 * Webhook Purchase.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Webhook Purchase.
 */
class YWT_SST_Webhook_Purchase {
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

		add_action( 'woocommerce_new_order', array( $this, 'woocommerce_new_order' ), 10, 2 );
	}

	/**
	 * New order create.
	 *
	 * @param  int      $order_id Order id.
	 * @param  WC_Order $order Order id.
	 * @return void
	 */
	public function woocommerce_new_order( $order_id, $order ) {
		if ( ! YWT_SST_Helpers::is_enable_webhook() ) {
			return;
		}

		if ( YWT_SST_FIELD_VALUE_YES !== YWT_SST_Helpers::get_option( YWT_SST_FIELD_WEBHOOKS_PURCHASE ) ) {
			return;
		}

		if ( ! ( $order instanceof WC_Order ) ) {
			return;
		}

		$request                              = array(
			'event'     => 'purchase',
			'ecommerce' => array(
				'transaction_id' => esc_attr( $order->get_order_number() ),
				'affiliation'    => '',
				'value'          => YWT_SST_WC_Helpers::instance()->formatted_price( $order->get_total() ),
				'tax'            => YWT_SST_WC_Helpers::instance()->formatted_price( $order->get_total_tax() ),
				'shipping'       => YWT_SST_WC_Helpers::instance()->formatted_price( $order->get_shipping_total() ),
				'currency'       => esc_attr( $order->get_currency() ),
				'coupon'         => esc_attr( join( ',', $order->get_coupon_codes() ) ),
				'items'          => YWT_SST_WC_Helpers::instance()->get_order_data_layer_items( $order->get_items() ),
			),
			'user_data' => YWT_SST_WC_Helpers::instance()->get_order_user_data( $order ),
		);
		$request['user_data']['new_customer'] 				= YWT_SST_WC_Helpers::instance()->is_new_customer( $order->get_customer_id() ) ? 'true' : 'false';
		$request['user_data']['lifetime_customer_value'] 	= YWT_SST_WC_Helpers::instance()->calculate_total_customer_lifetime_value( $order->get_customer_id() );


		$request_cookies = array(
			'_fbp'    => filter_input( INPUT_COOKIE, '_fbp', FILTER_DEFAULT ),
			'_fbc'    => filter_input( INPUT_COOKIE, '_fbc', FILTER_DEFAULT ),
			'FPGCLAW' => filter_input( INPUT_COOKIE, 'FPGCLAW', FILTER_DEFAULT ),
			'_gcl_aw' => filter_input( INPUT_COOKIE, '_gcl_aw', FILTER_DEFAULT ),
			'ttclid'  => filter_input( INPUT_COOKIE, 'ttclid', FILTER_DEFAULT ),
		);
		$request_cookies = array_filter( $request_cookies );

		if ( ! empty( $request_cookies ) ) {
			$request['cookies'] = $request_cookies;
		}

		YWT_SST_Helpers::send_webhook_request( $request );
	}
}
