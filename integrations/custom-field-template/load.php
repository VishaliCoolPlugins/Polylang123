<?php
/**
 * Loads the integration with Custom Field Template.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

add_action(
	'plugins_loaded',
	function () {
		if ( class_exists( 'custom_field_template' ) ) {
			LMAT_Integrations::instance()->cft = new LMAT_Cft();
			LMAT_Integrations::instance()->cft->init();
		}
	},
	0
);
