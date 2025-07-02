<?php
/**
 * Loads the integration with WordPress Importer.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

LMAT_Integrations::instance()->wp_importer = new LMAT_WordPress_Importer();
