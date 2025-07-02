<?php
/**
 * Loads the integration with Yoast SEO.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

add_action(
	'plugins_loaded',
	function () {
		if ( defined( 'WPSEO_VERSION' ) ) {
			add_action( 'lmat_init', array( LMAT_Integrations::instance()->wpseo = new LMAT_WPSEO(), 'init' ) );
		}
	},
	0
);
