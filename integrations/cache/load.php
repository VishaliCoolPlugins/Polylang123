<?php
/**
 * Loads the integration with cache plugins.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

add_action(
	'plugins_loaded',
	function () {
		if ( lmat_is_cache_active() ) {
			add_action( 'lmat_init', array( LMAT_Integrations::instance()->cache_compat = new LMAT_Cache_Compat(), 'init' ) );
		}
	},
	0
);
