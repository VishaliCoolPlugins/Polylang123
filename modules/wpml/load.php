<?php
/**
 * Loads the WPML compatibility mode.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

if ( $linguator->model->has_languages() ) {
	if ( ! defined( 'LMAT_WPML_COMPAT' ) || LMAT_WPML_COMPAT ) {
		LMAT_WPML_Compat::instance(); // WPML API.
		LMAT_WPML_Config::instance(); // wpml-config.xml.
	}
}
