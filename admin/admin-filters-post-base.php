<?php
/**
 * @package Linguator
 */

/**
 * Some common code for LMAT_Admin_Filters_Post and LMAT_Admin_Filters_Media
 *
 * @since 1.5
 */
abstract class LMAT_Admin_Filters_Post_Base {
	/**
	 * @var LMAT_Model
	 */
	public $model;

	/**
	 * @var LMAT_Admin_Links
	 */
	public $links;

	/**
	 * Language selected in the admin language filter.
	 *
	 * @var LMAT_Language|null
	 */
	public $filter_lang;

	/**
	 * Preferred language to assign to new contents.
	 *
	 * @var LMAT_Language|null
	 */
	public $pref_lang;

	/**
	 * Constructor: setups filters and actions
	 *
	 * @since 1.2
	 *
	 * @param object $linguator The Linguator object.
	 */
	public function __construct( &$linguator ) {
		$this->links = &$linguator->links;
		$this->model = &$linguator->model;
		$this->pref_lang = &$linguator->pref_lang;
	}

	/**
	 * Save translations from the languages metabox.
	 *
	 * @since 1.5
	 *
	 * @param int   $post_id Post id of the post being saved.
	 * @param int[] $arr     An array with language codes as key and post id as value.
	 * @return int[] The array of translated post ids.
	 */
	protected function save_translations( $post_id, $arr ) {
		// Security check as 'wp_insert_post' can be called from outside WP admin.
		check_admin_referer( 'lmat_language', '_lmat_nonce' );

		$translations = $this->model->post->save_translations( $post_id, $arr );
		return $translations;
	}
}
