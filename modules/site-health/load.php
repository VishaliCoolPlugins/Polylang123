<?php
/**
 * Loads the site health.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

if ( $linguator instanceof LMAT_Admin && $linguator->model->has_languages() ) {
	$linguator->site_health = new LMAT_Admin_Site_Health( $linguator );
}
