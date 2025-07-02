<?php
/**
 * @package Linguator
 */

namespace WP_Syntex\Linguator\REST;

defined( 'ABSPATH' ) || exit;

add_action(
	'lmat_init',
	function ( $linguator ) {
		$linguator->rest = new API( $linguator->model );
		add_action( 'rest_api_init', array( $linguator->rest, 'init' ) );
	}
);
