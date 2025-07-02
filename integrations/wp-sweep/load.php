<?php
/**
 * Loads the integration with WP Sweep.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

add_action(
	'plugins_loaded',
	function () {
		if ( defined( 'WP_SWEEP_VERSION' ) ) {
			LMAT_Integrations::instance()->wp_sweep = new LMAT_WP_Sweep();
			LMAT_Integrations::instance()->wp_sweep->init();
		}
	},
	0
);
