<?php
/**
 * Data Layer Event: view_item.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Data Layer Event: view_item.
 */
class YWT_SST_Event_ViewItem {
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
		if ( ! is_product() ) {
			return;
		}

		global $product;

		if ( ! ( $product instanceof WC_Product ) ) {
			return;
		}

		$data_layer = array(
			'event'     => 'view_item',
			'ecommerce' => array(
				'currency' => esc_attr( get_woocommerce_currency() ),
				'value'    => YWT_SST_WC_Helpers::instance()->formatted_price( $product->get_price() ),
				'items'    => array(
					YWT_SST_WC_Helpers::instance()->get_data_layer_item( $product ),
				),
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
