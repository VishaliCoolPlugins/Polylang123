<?php
/**
 * @package Linguator
 */

/**
 * Settings class for media language and translation management
 *
 * @since 1.8
 */
class LMAT_Settings_Media extends LMAT_Settings_Module {
	/**
	 * Stores the display order priority.
	 *
	 * @var int
	 */
	public $priority = 30;

	/**
	 * Constructor
	 *
	 * @since 1.8
	 *
	 * @param object $linguator linguator object
	 */
	public function __construct( &$linguator ) {
		parent::__construct(
			$linguator,
			array(
				'module'        => 'media',
				'title'         => __( 'Media', 'linguator' ),
				'description'   => __( 'Activate languages and translations for media only if you need to translate the text attached to the media: the title, the alternative text, the caption, the description... Note that the file is not duplicated.', 'linguator' ),
				'active_option' => 'media_support',
			)
		);
	}
}
