<?php
/**
 * Loads the module for general synchronization such as metas and taxonomies.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly
}

if ( $linguator->model->has_languages() ) {
	if ( $linguator instanceof LMAT_Admin_Base ) {
		$linguator->sync = new LMAT_Admin_Sync( $linguator );
	} else {
		$linguator->sync = new LMAT_Sync( $linguator );
	}

	add_filter(
		'lmat_settings_modules',
		function ( $modules ) {
			$modules[] = 'LMAT_Settings_Sync';
			return $modules;
		}
	);
}
