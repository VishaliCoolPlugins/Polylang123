<?php
/**
 * Loads the settings module for translated slugs.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly
}

if ( $linguator->model->has_languages() ) {
	add_filter(
		'lmat_settings_modules',
		function ( $modules ) {
			$modules[] = 'LMAT_Settings_Preview_Translate_Slugs';
			return $modules;
		}
	);
}
