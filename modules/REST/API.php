<?php
/**
 * @package Linguator
 */

namespace WP_Syntex\Linguator\REST;

use LMAT_Model;

defined( 'ABSPATH' ) || exit;

/**
 * Sets all Linguator REST controllers up.
 *
 * @since 3.7
 */
class API {
	/**
	 * REST languages.
	 *
	 * @var V1\Languages|null
	 */
	public $languages;

	/**
	 * REST settings.
	 *
	 * @var V1\Settings|null
	 */
	public $settings;

	/**
	 * @var LMAT_Model
	 */
	private $model;

	/**
	 * Constructor.
	 *
	 * @since 3.7
	 *
	 * @param LMAT_Model $model Linguator's model.
	 */
	public function __construct( LMAT_Model $model ) {
		$this->model = $model;
	}

	/**
	 * Adds hooks and registers endpoints.
	 *
	 * @since 3.7
	 *
	 * @return void
	 */
	public function init(): void {
		$this->languages = new V1\Languages( $this->model );
		$this->languages->register_routes();

		$this->settings = new V1\Settings( $this->model );
		$this->settings->register_routes();
	}
}
