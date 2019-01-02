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
	
	<?php if ( $purchases ) : ?>
	
		<?php foreach ( $purchases as $purchase ) : ?>
	
			<li>

				<div class="dpd-form">

					<strong><i class="icon-cart"></i> #<?php echo $purchase->id; ?></strong> - 
					<strong><span class="dpd-purchase-total">$<?php echo $purchase->total; ?></span></strong>

					<ul class="indent">
						
						<?php foreach ( $purchase->line_items as $download ) : ?>
						
							<li>

								<?php echo $download->product_name; ?>

								<ul class="indent">

										<li><?php _e( 'Downloaded:', 'pyis-dpd-helpscout' ); ?> <?php echo $download->download_count; ?></li>
										<li><?php _e( 'Can be downloaded:', 'pyis-dpd-helpscout' ); ?> <?php echo ( ! $download->download_limit ) ? __( 'Unlimited', 'pyis-dpd-helpscout' ) : $download->download_limit; ?></li>

								</ul>

							</li>
						
						<?php endforeach; ?>

					</ul>

				</div>

			</li>

		<?php endforeach; ?>
	
	<?php else : ?>
	
		<li>
			<?php printf( __( 'No purchases found for %s', 'pyis-dpd-helpscout' ), $email ); ?>
		</li>
	
	<?php endif; ?>
	
</ul>

<div class="divider"></div>

<?php do_action( 'pyis_helpscout_dpd_after_email_group', $email, $purchases ); ?>