<?php
/**
 * @package Linguator
 */

/**
 * Container for 3rd party plugins ( and themes ) integrations.
 * This class is available as soon as the plugin is loaded.
 *
 * @since 1.0
 * @since 2.8 Renamed from LMAT_Plugins_Compat to LMAT_Integrations.
 */
#[AllowDynamicProperties]
class LMAT_Integrations {
	/**
	 * Singleton instance.
	 *
	 * @var LMAT_Integrations|null
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 */
	protected function __construct() {}

	/**
	 * Returns the single instance of the class.
	 *
	 * @since 1.7
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Requires integrations.
	 *
	 * @since 3.7
	 *
	 * @return void
	 */
	protected function init(): void {
		// Loads external integrations.
		foreach ( glob( __DIR__ . '/*/load.php', GLOB_NOSORT ) as $load_script ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
			require_once $load_script; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		}
	}
}

class_alias( 'LMAT_Integrations', 'LMAT_Plugins_Compat' ); // For Backward compatibility.
