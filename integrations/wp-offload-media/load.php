<?php
/**
 * Loads the integration with WP Offload Media Lite.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

add_action(
	'plugins_loaded',
	function () {
		if ( function_exists( 'as3cf_init' ) && class_exists( 'LMAT_AS3CF' ) ) {
			add_action( 'lmat_init', array( LMAT_Integrations::instance()->as3cf = new LMAT_AS3CF(), 'init' ) );
		}
	},
	0
);
