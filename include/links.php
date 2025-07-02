<?php
/**
 * @package Linguator
 */

/**
 * Manages links related functions
 *
 * @since 1.2
 */
class LMAT_Links {
	/**
	 * Stores the plugin options.
	 *
	 * @var array
	 */
	public $options;

	/**
	 * @var LMAT_Model
	 */
	public $model;

	/**
	 * Instance of a child class of LMAT_Links_Model.
	 *
	 * @var LMAT_Links_Model
	 */
	public $links_model;

	/**
	 * Current language (used to filter the content).
	 *
	 * @var LMAT_Language|null
	 */
	public $curlang;

	/**
	 * Constructor
	 *
	 * @since 1.2
	 *
	 * @param object $linguator The Linguator object.
	 */
	public function __construct( &$linguator ) {
		$this->links_model = &$linguator->links_model;
		$this->model = &$linguator->model;
		$this->options = &$linguator->options;
	}

	/**
	 * Returns the home url in the requested language.
	 *
	 * @since 1.3
	 *
	 * @param LMAT_Language|string $language  The language.
	 * @param bool                $is_search Optional, whether we need the home url for a search form, defaults to false.
	 * @return string
	 */
	public function get_home_url( $language, $is_search = false ) {
		if ( ! $language instanceof LMAT_Language ) {
			$language = $this->model->get_language( $language );
		}

		if ( empty( $language ) ) {
			return home_url( '/' );
		}

		return $is_search ? $language->get_search_url() : $language->get_home_url();
	}
}
