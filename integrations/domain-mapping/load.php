<?php
/**
 * Loads the integration with WordPress MU Domain Mapping.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

LMAT_Integrations::instance()->dm = new LMAT_Domain_Mapping();
