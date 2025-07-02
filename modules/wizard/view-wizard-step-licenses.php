<?php
/**
 * Displays the wizard licenses step
 *
 * @package Linguator
 *
 * @since 2.7
 */

defined( 'ABSPATH' ) || exit;

$licenses = apply_filters( 'lmat_settings_licenses', array() );
$is_error = isset( $_GET['activate_error'] ) && 'i18n_license_key_error' === sanitize_key( $_GET['activate_error'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<p>
	<?php esc_html_e( 'You are using plugins which require a license key.', 'linguator' ); ?>
	<?php
	if ( 1 === count( $licenses ) ) {
		echo esc_html( __( 'Please enter your license key:', 'linguator' ) );
	} else {
		echo esc_html( __( 'Please enter your license keys:', 'linguator' ) );
	}
	?>
</p>
<h2><?php esc_html_e( 'Licenses', 'linguator' ); ?></h2>
<div id="messages">
	<?php if ( $is_error ) : ?>
		<p class="error"><?php esc_html_e( 'There is an error with a license key.', 'linguator' ); ?></p>
	<?php endif; ?>
</div>
<div class="form-field">
	<table id="lmat-licenses-table" class="form-table lmat-table-top">
		<tbody>
		<?php
		foreach ( $licenses as $license ) {
			// Escaping is already done in get_form_field method.
			echo $license->get_form_field(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
		</tbody>
	</table>
</div>
