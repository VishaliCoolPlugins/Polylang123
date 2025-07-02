<?php
/**
 * @package Linguator
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class to advertize the Machine Translation module.
 *
 * @since 3.6
 */
class LMAT_Settings_Preview_Machine_Translation extends LMAT_Settings_Module {
	/**
	 * Stores the display order priority.
	 *
	 * @var int
	 */
	public $priority = 90;

	/**
	 * Constructor.
	 *
	 * @since 3.6
	 *
	 * @param LMAT_Settings $linguator Linguator object.
	 * @param array        $args     Optional. Addition arguments.
	 *
	 * @phpstan-param array{
	 *   module?: non-falsy-string,
	 *   title?: string,
	 *   description?: string,
	 *   active_option?: non-falsy-string
	 * } $args
	 */
	public function __construct( &$linguator, array $args = array() ) {
		$default = array(
			'module'        => 'machine_translation',
			'title'         => __( 'Machine Translation', 'linguator' ),
			'description'   => __( 'Allows linkage to DeepL Translate.', 'linguator' ),
			'active_option' => 'preview',
		);

		parent::__construct( $linguator, array_merge( $default, $args ) );
	}
}
