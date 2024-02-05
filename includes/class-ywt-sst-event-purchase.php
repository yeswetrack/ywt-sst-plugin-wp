<?php
/**
 * Data Layer Event: purchase.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Data Layer Event: purchase.
 */
class YWT_SST_Event_Purchase {
	use YWT_SST_Singleton;

	/**
	 * Session transaction key.
	 *
	 * @var string
	 */
	const TRANSACTION_KEY = 'ywt_sst_order_id';

	/**
	 * Check order created or not.
	 *
	 * @var bool
	 */
	private $is_order_created = false;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! YWT_SST_WC_Helpers::instance()->is_enable_ecommerce() ) {
			return;
		}

		add_action( 'woocommerce_new_order', array( $this, 'woocommerce_new_order' ) );
		add_action( 'woocommerce_thankyou', array( $this, 'woocommerce_new_order' ) );
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
	}

	/**
	 * New order create.
	 *
	 * @param  int $order_id Order id.
	 * @return void
	 */
	public function woocommerce_new_order( $order_id ) {
		if ( $this->is_order_created ) {
			return;
		}
		$this->is_order_created = true;

		YWT_SST_Helpers::set_session( self::TRANSACTION_KEY, $order_id );
	}

	/**
	 * WP footer hook.
	 *
	 * @return void
	 */
	public function wp_footer() {
		$order_id = YWT_SST_Helpers::get_session( self::TRANSACTION_KEY );
		if ( empty( $order_id ) ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! ( $order instanceof WC_Order ) ) {
			return;
		}

		$data_layer = array(
			'event'     => 'purchase',
			'ecommerce' => array(
				'transaction_id'  => esc_attr( $order->get_order_number() ),
				'affiliation'     => '',
				'value'           => YWT_SST_WC_Helpers::instance()->formatted_price( $order->get_total() ),
				'tax'             => YWT_SST_WC_Helpers::instance()->formatted_price( $order->get_total_tax() ),
				'shipping'        => YWT_SST_WC_Helpers::instance()->formatted_price( $order->get_shipping_total() ),
				'currency'        => esc_attr( $order->get_currency() ),
				'coupon'          => esc_attr( join( ',', $order->get_coupon_codes() ) ),
				'discount_amount' => YWT_SST_WC_Helpers::instance()->formatted_price( $order->get_discount_total() ),
				'items'           => YWT_SST_WC_Helpers::instance()->get_order_data_layer_items( $order->get_items() ),
			),
		);

		if ( YWT_SST_WC_Helpers::instance()->is_enable_user_data() ) {
			$data_layer['user_data']        				        = YWT_SST_WC_Helpers::instance()->get_order_user_data( $order );
			$data_layer['user_data']['new_customer'] 				= YWT_SST_WC_Helpers::instance()->is_new_customer( $order->get_customer_id() ) ? 'true' : 'false';
			$data_layer['user_data']['lifetime_customer_value'] 	= YWT_SST_WC_Helpers::instance()->calculate_total_customer_lifetime_value( $order->get_customer_id() );
		}
		?>
		<script type="text/javascript">
			dataLayer.push(<?php echo YWT_SST_Helpers::array_to_json( $data_layer ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>);
		</script>
		<?php
		YWT_SST_Helpers::javascript_delete_cookie( self::TRANSACTION_KEY );
	}
}
