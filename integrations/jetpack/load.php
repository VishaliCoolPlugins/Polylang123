<?php
/**
 * Loads the integration with Jetpack.
 * Works for Twenty Fourteen featured content too.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

LMAT_Integrations::instance()->jetpack = new LMAT_Jetpack(); // Must be loaded before the plugin is active.
add_action( 'lmat_init', array( LMAT_Integrations::instance()->featured_content = new LMAT_Featured_Content(), 'init' ) );
