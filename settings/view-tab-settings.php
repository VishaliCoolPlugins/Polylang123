<?php
/**
 * Displays the settings tab in Linguator settings
 *
 * @package Linguator
 *
 * @var LMAT_Settings_Module[] $modules List of Linguator modules.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="form-wrap">
	<?php
	wp_nonce_field( 'lmat_options', '_lmat_nonce' );
	$list_table = new LMAT_Table_Settings();
	$list_table->prepare_items( $modules );
	$list_table->display();
	?>
</div>
