<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    YWT_SST
 * @subpackage YWT_SST/admin/partials
 */

$tab = YWT_SST_Admin_Settings::get_settings_tab(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
?>

<div id="ywt-sst-admin-settings" class="wrap">
	<h2><?php esc_html_e( 'YesWeTrack SST for Wordpress', 'ywt-sst' ); ?></h2>

	<div class="tabinfo">
		<strong>
			<?php esc_html_e( 'This plugin is made for customers of YesWeTrack, if you need help ', 'ywt-sst' ); ?>
			<a href="https://yeswetrack.com/contact/" target="_blank">
				<?php esc_html_e( 'contact us here', 'ywt-sst' ); ?>
			</a>.
		</strong>
	</div>

	<div class="nav-tab-wrapper wp-clearfix">
		<a href="<?php echo remove_query_arg( 'tab' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="nav-tab<?php echo 'general' === $tab ? ' nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'General', 'ywt-sst' ); ?>
		</a>
		<?php if ( YWT_SST_Helpers::is_plugin_wc_enabled() ) : ?>
			<a href="<?php echo add_query_arg( array( 'tab' => 'data-layer' ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="nav-tab<?php echo 'data-layer' === $tab ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Data Layer', 'ywt-sst' ); ?>
			</a>
		<?php else : ?>
			<div class="nav-tab tab-disabled" title="<?php esc_html_e( 'Activate WooCommerce plugin', 'ywt-sst' ); ?>">
				<?php esc_html_e( 'Data Layer', 'ywt-sst' ); ?>
			</div>
		<?php endif; ?>
		<?php if ( YWT_SST_Helpers::is_plugin_wc_enabled() ) : ?>
			<a href="<?php echo add_query_arg( array( 'tab' => 'webhooks' ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="nav-tab<?php echo 'webhooks' === $tab ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Webhooks', 'ywt-sst' ); ?>
			</a>
		<?php else : ?>
			<div class="nav-tab tab-disabled" title="<?php esc_html_e( 'Activate WooCommerce plugin', 'ywt-sst' ); ?>">
				<?php esc_html_e( 'Webhooks', 'ywt-sst' ); ?>
			</div>
		<?php endif; ?>
	</div>

	<form action="options.php" method="post" class="js-form-ywt-sst">
		<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>" ?>
		<?php settings_fields( YWT_SST_ADMIN_GROUP ); ?>
		<?php do_settings_sections( YWT_SST_ADMIN_SLUG ); ?>

		<?php if ( 'webhooks' === $tab ) : ?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th style="font-weight: normal;">
							<button type="button" class="button | js-send-test-webhooks">
								<?php esc_html_e( 'Send test webhook', 'ywt-sst' ); ?>
							</button>
							<p>
								<?php esc_html_e( 'If you have made changes to the settings, first save them before sending the test.', 'ywt-sst' ); ?>
							</p>
						</th>
						<td class="js-ajax-message" data-message-loading="<?php esc_html_e( 'Sending...', 'ywt-sst' ); ?>"></td>
					</tr>
				</tbody>
			</table>
		<?php endif; ?>

		<?php submit_button(); ?>
	</form>
</div>
