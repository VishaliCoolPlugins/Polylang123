<?php
/**
 * Loads the settings module for Machine Translation.
 *
 * @package Linguator
 */

defined( 'ABSPATH' ) || exit;

if ( $linguator->model->has_languages() ) {
	add_filter(
		'lmat_settings_modules',
		function ( $modules ) {
			$modules[] = 'LMAT_Settings_Preview_Machine_Translation';
			return $modules;
		}
	);
}
