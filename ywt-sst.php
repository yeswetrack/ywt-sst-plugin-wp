<?php
/**
 * Main plugin file.
 *
 * @link              https://yeswetrack.com/
 * @since             1.0.0
 * @package           YWT_SST
 *
 * @wordpress-plugin
 * Plugin Name:       YesWeTrack SST
 * Description:       Enhance conversion tracking by implementing server-side tagging using server Google Tag Manager container. Effortlessly configure data layer events in web GTM, send webhooks, set up custom loader, and extend cookie lifetime.
 * Version:           1.0
 * Author:            YesWeTrack
 * Author URI:        https://yeswetrack.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ywt-sst
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bootstrap.
 */
require plugin_dir_path( __FILE__ ) . 'bootstrap.php';

register_activation_hook( __FILE__, array( YWT_SST_Plugin_Activate::class, 'instance' ) );

add_action( 'init', array( YWT_SST_Plugin_Upgrade::class, 'instance' ) );
add_action( 'ywt_sst', array( YWT_SST_I18n::class, 'instance' ) );
add_action( 'ywt_sst', array( YWT_SST_Webhook_Purchase::class, 'instance' ) );
add_action( 'ywt_sst', array( YWT_SST_Webhook_Refund::class, 'instance' ) );
add_action( 'ywt_sst_admin', array( YWT_SST_Admin_Settings::class, 'instance' ) );
add_action( 'ywt_sst_admin', array( YWT_SST_Admin_Ajax::class, 'instance' ) );
add_action( 'ywt_sst_admin', array( YWT_SST_Admin_Assets::class, 'instance' ) );
add_action( 'ywt_sst_frontend', array( YWT_SST_Frontend_Assets::class, 'instance' ) );
add_action( 'ywt_sst_frontend', array( YWT_SST_Tracking_Code::class, 'instance' ) );
add_action( 'ywt_sst_frontend', array( YWT_SST_Tracking_Gtm4wp::class, 'instance' ) );
add_action( 'ywt_sst_frontend', array( YWT_SST_Event_Login::class, 'instance' ) );
add_action( 'ywt_sst_frontend', array( YWT_SST_Event_Login::class, 'instance' ) );
add_action( 'ywt_sst_frontend', array( YWT_SST_Event_Register::class, 'instance' ) );
add_action( 'ywt_sst_frontend', array( YWT_SST_Event_ViewItem::class, 'instance' ) );
add_action( 'ywt_sst_frontend', array( YWT_SST_Event_ViewCart::class, 'instance' ) );
add_action( 'ywt_sst_frontend', array( YWT_SST_Event_BeginCheckout::class, 'instance' ) );
add_action( 'ywt_sst_frontend', array( YWT_SST_Event_Purchase::class, 'instance' ) );
add_action( 'ywt_sst_frontend', array( YWT_SST_Event_AddToCart::class, 'instance' ) );
