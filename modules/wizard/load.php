<?php
/**
 * Loads the setup wizard.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly
}

if ( $linguator instanceof LMAT_Admin_Base ) {
	$linguator->wizard = new LMAT_Wizard( $linguator );
}
