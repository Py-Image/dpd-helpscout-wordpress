<?php
/**
 * HTML View for DPD Purchases within Helpscout
 *
 * @since 1.0.0
 *
 * @package PyIS_DPD_HelpScout
 * @subpackage PyIS_DPD_HelpScout/core/views
 */

defined( 'ABSPATH' ) || die();

?>
	
<?php do_action( 'pyis_helpscout_dpd_before_email_group', $email, $purchases ); ?>

<strong><?php echo $email; ?></strong>
<br /><br />

<ul class="unstyled">
	<?php foreach ( $purchases as $purchase ) : ?>
	
		<?php foreach ( $purchase->line_items as $download ) : ?>
	
			<li>
				
				<div class="dpd-form">
				
					<?php echo $download->product_name; ?> - 

					<a href="#" class="dpd-regenerate" title="<?php _e( 'Regenerate Access', 'pyis-dpd-helpscout' ); ?>">
						<span class="badge green"><?php _e( 'Regenerate Access', 'pyis-dpd-helpscout' ); ?></span>
					</a>

					<span class="hidden-input product_id" style="display: none;"><?php echo $download->product_id; ?></span>
					<span class="hidden-input purchase_id" style="display: none;"><?php echo $download->purchase_id; ?></span>
					<span class="hidden-input id" style="display: none;"><?php echo $download->id; ?></span>
					<span class="hidden-input customer_id" style="display: none;"><?php echo $purchase->customer->id; ?></span>
					<span class="hidden-input helpscout-secret-key" style="display: none;"><?php echo PyIS_DPD_HelpScout_REST::hash_secret_key( get_option( 'pyis_dpd_helpscout_secret_key' ), $helpscout_data ); ?></span>

					<ul class="indent">

						<li><?php _e( 'Downloaded:', 'pyis-dpd-helpscout' ); ?> <?php echo $download->download_count; ?></li>
						<li><?php _e( 'Can be downloaded:', 'pyis-dpd-helpscout' ); ?> <?php echo ( ! $download->download_limit ) ? __( 'Unlimited', 'pyis-dpd-helpscout' ) : $download->download_limit; ?></li>
						<li><?php _e( 'Expires:', 'pyis-dpd-helpscout' ); ?> <?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $download->expires_at ), get_option( 'date_format', 'F j, Y' ) . ' @ ' . get_option( 'time_format', 'g:i a' ) . ' T' ); ?></li>

					</ul>
					
				</div>

			</li>
	
		<?php endforeach; ?>
	
	<?php endforeach; ?>
</ul>

<div class="divider"></div>

<?php do_action( 'pyis_helpscout_dpd_after_email_group', $email, $purchases ); ?>