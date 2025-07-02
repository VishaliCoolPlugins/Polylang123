<?php
/**
 * Loads the integration with WordPress MU Domain Mapping.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

add_action( 'init', array( LMAT_Integrations::instance()->twenty_seventeen = new LMAT_Twenty_Seventeen(), 'init' ) );
