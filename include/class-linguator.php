<?php
/**
 * @package Linguator
 */

use WP_Syntex\Linguator\Options\Options;
use WP_Syntex\Linguator\Options\Registry as Options_Registry;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly
}

// Default directory to store user data such as custom flags
if ( ! defined( 'LMAT_LOCAL_DIR' ) ) {
	define( 'LMAT_LOCAL_DIR', WP_CONTENT_DIR . '/linguator' );
}

// Includes local config file if exists
if ( is_readable( LMAT_LOCAL_DIR . '/lmat-config.php' ) ) {
	include_once LMAT_LOCAL_DIR . '/lmat-config.php';
}

/**
 * Controls the plugin, as well as activation, and deactivation
 *
 * @since 0.1
 *
 * @template TLMATClass of LMAT_Base
 */
class Linguator {

	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	public function __construct() {
		require_once __DIR__ . '/functions.php'; // VIP functions

		// register an action when plugin is activating.
		register_activation_hook( LINGUATOR_BASENAME, array( 'LMAT_Wizard', 'start_wizard' ) );

		$install = new LMAT_Install( LINGUATOR_BASENAME );

		// Stopping here if we are going to deactivate the plugin ( avoids breaking rewrite rules )
		if ( $install->is_deactivation() || ! $install->can_activate() ) {
			return;
		}

		// Plugin initialization
		// Take no action before all plugins are loaded
		add_action( 'plugins_loaded', array( $this, 'init' ), 1 );

		// Override load text domain waiting for the language to be defined
		// Here for plugins which load text domain as soon as loaded :(
		if ( ! defined( 'LMAT_OLT' ) || LMAT_OLT ) {
			LMAT_OLT_Manager::instance();
		}

		/*
		 * Loads the compatibility with some plugins and themes.
		 * Loaded as soon as possible as we may need to act before other plugins are loaded.
		 */
		if ( ! defined( 'LMAT_PLUGINS_COMPAT' ) || LMAT_PLUGINS_COMPAT ) {
			LMAT_Integrations::instance();
		}
	}

	/**
	 * Tells whether the current request is an ajax request on frontend or not
	 *
	 * @since 2.2
	 *
	 * @return bool
	 */
	public static function is_ajax_on_front() {
		// Special test for plupload which does not use jquery ajax and thus does not pass our ajax prefilter
		// Special test for customize_save done in frontend but for which we want to load the admin
		$in = isset( $_REQUEST['action'] ) && in_array( sanitize_key( $_REQUEST['action'] ), array( 'upload-attachment', 'customize_save' ) ); // phpcs:ignore WordPress.Security.NonceVerification
		$is_ajax_on_front = wp_doing_ajax() && empty( $_REQUEST['lmat_ajax_backend'] ) && ! $in; // phpcs:ignore WordPress.Security.NonceVerification

		/**
		 * Filters whether the current request is an ajax request on front.
		 *
		 * @since 2.3
		 *
		 * @param bool $is_ajax_on_front Whether the current request is an ajax request on front.
		 */
		return apply_filters( 'lmat_is_ajax_on_front', $is_ajax_on_front );
	}

	/**
	 * Is the current request a REST API request?
	 * Inspired by WP::parse_request()
	 * Needed because at this point, the constant REST_REQUEST is not defined yet
	 *
	 * @since 2.4.1
	 *
	 * @return bool
	 */
	public static function is_rest_request() {
		// Handle pretty permalinks.
		$home_path       = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
		$home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

		$req_uri = trim( (string) wp_parse_url( lmat_get_requested_url(), PHP_URL_PATH ), '/' );
		$req_uri = (string) preg_replace( $home_path_regex, '', $req_uri );
		$req_uri = trim( $req_uri, '/' );
		$req_uri = str_replace( 'index.php', '', $req_uri );
		$req_uri = trim( $req_uri, '/' );

		// And also test rest_route query string parameter is not empty for plain permalinks.
		$query_string = array();
		wp_parse_str( (string) wp_parse_url( lmat_get_requested_url(), PHP_URL_QUERY ), $query_string );
		$rest_route = isset( $query_string['rest_route'] ) && is_string( $query_string['rest_route'] ) ? trim( $query_string['rest_route'], '/' ) : false;

		return 0 === strpos( $req_uri, rest_get_url_prefix() . '/' ) || ! empty( $rest_route );
	}

	/**
	 * Tells if we are in the wizard process.
	 *
	 * @since 2.7
	 *
	 * @return bool
	 */
	public static function is_wizard() {
		return isset( $_GET['page'] ) && ! empty( $_GET['page'] ) && 'mlang_wizard' === sanitize_key( $_GET['page'] ); // phpcs:ignore WordPress.Security.NonceVerification
	}

	/**
	 * Defines constants
	 * May be overridden by a plugin if set before plugins_loaded, 1
	 *
	 * @since 1.6
	 *
	 * @return void
	 */
	public static function define_constants() {
		// Cookie name. no cookie will be used if set to false
		if ( ! defined( 'LMAT_COOKIE' ) ) {
			define( 'LMAT_COOKIE', 'lmat_language' );
		}

		// Backward compatibility with Linguator < 2.3
		if ( ! defined( 'LMAT_AJAX_ON_FRONT' ) ) {
			define( 'LMAT_AJAX_ON_FRONT', self::is_ajax_on_front() );
		}

		// Admin
		if ( ! defined( 'LMAT_ADMIN' ) ) {
			define( 'LMAT_ADMIN', wp_doing_cron() || ( defined( 'WP_CLI' ) && WP_CLI ) || ( is_admin() && ! LMAT_AJAX_ON_FRONT ) );
		}

		// Settings page whatever the tab except for the wizard which needs to be an admin process.
		if ( ! defined( 'LMAT_SETTINGS' ) ) {
			define( 'LMAT_SETTINGS', is_admin() && ( ( isset( $_GET['page'] ) && 0 === strpos( sanitize_key( $_GET['page'] ), 'mlang' ) && ! self::is_wizard() ) || ! empty( $_REQUEST['lmat_ajax_settings'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}
	}

	/**
	 * Linguator initialization
	 * setups models and separate admin and frontend
	 *
	 * @since 1.2
	 *
	 * @return void
	 */
	public function init() {
		self::define_constants();

		// Plugin options.
		add_action( 'lmat_init_options_for_blog', array( Options_Registry::class, 'register' ) );
		$options = new Options();

		// Plugin upgrade
		if ( ! empty( $options['version'] ) ) {
			if ( version_compare( $options['version'], LINGUATOR_VERSION, '<' ) ) {
				$upgrade = new LMAT_Upgrade( $options );
				if ( ! $upgrade->upgrade() ) { // If the version is too old
					return;
				}
			}
		} else {
			// In some edge cases, it's possible that no options were found in the database.
			$options['version'] = LINGUATOR_VERSION;
		}

		/**
		 * Filter the model class to use
		 * /!\ this filter is fired *before* the $linguator object is available
		 *
		 * @since 1.5
		 *
		 * @param string $class either LMAT_Model or LMAT_Admin_Model
		 */
		$class = apply_filters( 'lmat_model', LMAT_SETTINGS || self::is_wizard() ? 'LMAT_Admin_Model' : 'LMAT_Model' );
		/** @var LMAT_Model $model */
		$model = new $class( $options );

		if ( ! $model->has_languages() ) {
			/**
			 * Fires when no language has been defined yet
			 * Used to load overridden textdomains
			 *
			 * @since 1.2
			 */
			do_action( 'lmat_no_language_defined' );
		}

		$class = '';

		if ( LMAT_SETTINGS ) {
			$class = 'LMAT_Settings';
		} elseif ( LMAT_ADMIN ) {
			$class = 'LMAT_Admin';
		} elseif ( self::is_rest_request() ) {
			$class = 'LMAT_REST_Request';
		} elseif ( $model->has_languages() ) {
			$class = 'LMAT_Frontend';
		}

		/**
		 * Filters the class to use to instantiate the $linguator object
		 *
		 * @since 2.6
		 *
		 * @param string $class A class name.
		 */
		$class = apply_filters( 'lmat_context', $class );

		if ( ! empty( $class ) ) {
			/** @phpstan-var class-string<TLMATClass> $class */
			$this->init_context( $class, $model );
		}
	}

	/**
	 * Linguator initialization.
	 * Setups the Linguator Context, loads the modules and init Linguator.
	 *
	 * @since 3.6
	 *
	 * @param string    $class The class name.
	 * @param LMAT_Model $model Instance of LMAT_Model.
	 * @return LMAT_Base
	 *
	 * @phpstan-param class-string<TLMATClass> $class
	 * @phpstan-return TLMATClass
	 */
	public function init_context( string $class, LMAT_Model $model ): LMAT_Base {
		global $linguator;

		$links_model = $model->get_links_model();
		$linguator    = new $class( $links_model );

		/**
		 * Fires after Linguator's model init.
		 * This is the best place to register a custom table (see `LMAT_Model`'s constructor).
		 * /!\ This hook is fired *before* the $linguator object is available.
		 * /!\ The languages are also not available yet.
		 *
		 * @since 3.4
		 *
		 * @param LMAT_Model $model Linguator model.
		 */
		do_action( 'lmat_model_init', $model );

		$model->maybe_create_language_terms();

		/**
		 * Fires after the $linguator object is created and before the API is loaded
		 *
		 * @since 2.0
		 *
		 * @param object $linguator
		 */
		do_action_ref_array( 'lmat_pre_init', array( &$linguator ) );

		// Loads the API
		require_once LINGUATOR_DIR . '/include/api.php';

		// Loads the modules.
		$load_scripts = glob( LINGUATOR_DIR . '/modules/*/load.php', GLOB_NOSORT );
		if ( is_array( $load_scripts ) ) {
			foreach ( $load_scripts as $load_script ) {
				require_once $load_script; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
			}
		}

		$linguator->init();

		/**
		 * Fires after the $linguator object and the API is loaded
		 *
		 * @since 1.7
		 *
		 * @param object $linguator
		 */
		do_action_ref_array( 'lmat_init', array( &$linguator ) );

		return $linguator;
	}
}
