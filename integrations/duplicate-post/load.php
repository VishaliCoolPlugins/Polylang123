<?php
/**
 * Loads the integration with Duplicate Post.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

add_action(
	'plugins_loaded',
	function () {
		if ( defined( 'DUPLICATE_POST_CURRENT_VERSION' ) ) {
			LMAT_Integrations::instance()->duplicate_post = new LMAT_Duplicate_Post();
			LMAT_Integrations::instance()->duplicate_post->init();
		}
	},
	0
);
