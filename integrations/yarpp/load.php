<?php
/**
 * Loads the integration with Yet Another Related Posts Plugin.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

add_action(
	'plugins_loaded',
	function () {
		if ( defined( 'YARPP_VERSION' ) ) {
			add_action( 'init', array( LMAT_Integrations::instance()->yarpp = new LMAT_Yarpp(), 'init' ) );
		}
	},
	0
);
