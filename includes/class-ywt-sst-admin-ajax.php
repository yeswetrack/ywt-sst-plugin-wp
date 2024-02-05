<?php
/**
 * Assets for admin.
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/includes
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Assets for admin.
 */
class YWT_SST_Admin_Ajax {
	use YWT_SST_Singleton;

	/**
	 * Container url.
	 *
	 * @var string
	 */
	private $container_url;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! YWT_SST_Helpers::is_enable_webhook() ) {
			return;
		}

		add_action( 'wp_ajax_ywt_sst_webhook_test', array( $this, 'ywt_sst_webhook_test' ) );
	}

	/**
	 * Test webhook
	 *
	 * @return void
	 */
	public function ywt_sst_webhook_test() {
		check_ajax_referer( YWT_SST_AJAX_SECURITY, 'security' );

		remove_action( 'wp_ajax_ywt_sst_webhook_test', array( $this, 'ywt_sst_webhook_test' ) );

		$this->container_url = YWT_SST_Helpers::get_option( YWT_SST_FIELD_WEBHOOKS_CONTAINER_URL );
		if ( empty( $this->container_url ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'GTM server container URL is required.', 'ywt-sst' ),
				)
			);
		}

		$is_purchase = YWT_SST_Helpers::get_option( YWT_SST_FIELD_WEBHOOKS_PURCHASE );
		$is_refund   = YWT_SST_Helpers::get_option( YWT_SST_FIELD_WEBHOOKS_REFUND );
		if ( empty( $is_purchase ) && empty( $is_refund ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Purchase or refund webhook is required.', 'ywt-sst' ),
				)
			);
		}

		$answer = array();
		if ( ! empty( $is_purchase ) ) {
			$request = array(
				'event'     => 'purchase',
				'ecommerce' => array(
					'transaction_id' => '358',
					'affiliation'    => 'test',
					'value'          => 29.00,
					'tax'            => 0,
					'shipping'       => 0,
					'currency'       => 'EUR',
					'coupon'         => 'test_coupon',
					'items'          => array(
						array(
							'item_name'      => 'Beanie',
							'item_brand'     => 'YWT',
							'item_id'        => '15',
							'item_sku'       => 'woo-beanie',
							'price'          => 18.00,
							'item_category'  => 'Clothing',
							'item_category2' => 'Accessories',
							'quantity'       => 1,
							'index'          => 1,
						),
					),
				),
			);

			$result = $this->send_request( $request );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Some problem with Purchase webhook.', 'ywt-sst' ),
					)
				);
			}
			$answer[] = __( 'Purchase webhook sent.', 'ywt-sst' );
		}

		if ( ! empty( $is_refund ) ) {
			$request = array(
				'event'     => 'refund',
				'ecommerce' => array(
					'transaction_id' => '358',
					'value'          => 18.00,
					'currency'       => 'USD',
					'items'          => array(
						array(
							'item_name'      => 'Beanie',
							'item_brand'     => 'YWT',
							'item_id'        => '15',
							'item_sku'       => 'woo-beanie',
							'price'          => 18.00,
							'item_category'  => 'Clothing',
							'item_category2' => 'Accessories',
							'quantity'       => 1,
							'index'          => 1,
						),
					),
				),
			);

			$result = $this->send_request( $request );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Some problem with Refund webhook.', 'ywt-sst' ),
					)
				);
			}
			$answer[] = __( 'Refund webhook sent.', 'ywt-sst' );
		}

		try {
			wp_send_json_success(
				array(
					'message' => join( ' ', $answer ),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error(
				array(
					'message' => __( 'An error occurred during data processing.', 'ywt-sst' ),
				)
			);
		}
		exit;
	}

	/**
	 * Send request.
	 *
	 * @param  array $body post data.
	 * @return array|WP_Error The response or WP_Error on failure.
	 */
	private function send_request( $body ) {
		$body['user_data'] = $this->get_request_user_data();

		return wp_remote_post(
			$this->container_url,
			array(
				'headers' => array(
					'cache-control' => 'no-cache',
					'content-type'  => 'application/json',
				),
				'body'    => wp_json_encode( $body ),
			)
		);
	}

	/**
	 * Return user request test data
	 *
	 * @return array
	 */
	private function get_request_user_data() {
		return array(
			'customer_id'         => 69,
			'billing_first_name'  => 'Test',
			'billing_last_name'   => 'Name',
			'billing_address'     => '3601 Old Capitol Trail',
			'billing_postcode'    => '19808',
			'billing_country'     => 'US',
			'billing_state'       => 'Delaware',
			'billing_city'        => 'Wilmington',
			'billing_email'       => 'mytest@example.com',
			'billing_phone'       => '380999222212',
			'shipping_first_name' => 'Test',
			'shipping_last_name'  => 'Name',
			'shipping_company'    => 'Company',
			'shipping_address'    => '3601 Old Capitol Trail',
			'shipping_postcode'   => '19808',
			'shipping_country'    => 'US',
			'shipping_state'      => 'Delaware',
			'shipping_city'       => 'Wilmington',
			'shipping_phone'      => '380999222212',
			'email'               => 'mytest@example.com',
			'first_name'          => 'Test',
			'last_name'           => 'Name',
			'new_customer'        => 'false',
		);
	}
}
