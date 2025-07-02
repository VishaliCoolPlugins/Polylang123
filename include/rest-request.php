<?php
/**
 * @package Linguator
 */

/**
 * Main Linguator class for REST API requests, accessible from @see LMAT().
 *
 * @since 2.6
 */
class LMAT_REST_Request extends LMAT_Base {
	/**
	 * @var LMAT_Language|false|null A `LMAT_Language` when defined, `false` otherwise. `null` until the language
	 *                              definition process runs.
	 */
	public $curlang;

	/**
	 * @var LMAT_Default_Term|null
	 */
	public $default_term;

	/**
	 * @var LMAT_Filters|null
	 */
	public $filters;

	/**
	 * @var LMAT_Filters_Links|null
	 */
	public $filters_links;

	/**
	 * @var LMAT_Admin_Links|null
	 */
	public $links;

	/**
	 * @var LMAT_Nav_Menu|null
	 */
	public $nav_menu;

	/**
	 * @var LMAT_Static_Pages|null
	 */
	public $static_pages;

	/**
	 * @var LMAT_Filters_Widgets_Options|null
	 */
	public $filters_widgets_options;

	/**
	 * Constructor.
	 *
	 * @since 3.4
	 *
	 * @param LMAT_Links_Model $links_model Reference to the links model.
	 */
	public function __construct( &$links_model ) {
		parent::__construct( $links_model );

		// Static front page and page for posts.
		// Early instantiated to be able to correctly initialize language properties.
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$this->static_pages = new LMAT_Static_Pages( $this );
		}

		$this->model->set_languages_ready();
	}

	/**
	 * Setup filters.
	 *
	 * @since 2.6
	 *
	 * @return void
	 */
	public function init() {
		parent::init();

		$this->default_term = new LMAT_Default_Term( $this );
		$this->default_term->add_hooks();

		if ( ! $this->model->has_languages() ) {
			return;
		}

		add_filter( 'rest_pre_dispatch', array( $this, 'set_language' ), 10, 3 );

		$this->filters_links           = new LMAT_Filters_Links( $this );
		$this->filters                 = new LMAT_Filters( $this );
		$this->filters_widgets_options = new LMAT_Filters_Widgets_Options( $this );

		$this->links    = new LMAT_Admin_Links( $this );
		$this->nav_menu = new LMAT_Frontend_Nav_Menu( $this ); // For auto added pages to menu.
	}

	/**
	 * Sets the current language during a REST request if sent.
	 *
	 * @since 3.3
	 *
	 * @param mixed           $result  Response to replace the requested version with. Remains untouched.
	 * @param WP_REST_Server  $server  Server instance.
	 * @param WP_REST_Request $request Request used to generate the response.
	 * @return mixed Untouched $result.
	 *
	 * @phpstan-param WP_REST_Request<array{lang?: string}> $request
	 */
	public function set_language( $result, $server, $request ) {
		$lang = $request->get_param( 'lang' );

		if ( ! empty( $lang ) && is_string( $lang ) ) {
			$this->curlang = $this->model->get_language( sanitize_key( $lang ) );

			if ( empty( $this->curlang ) && ! empty( $this->options['default_lang'] ) ) {
				// A lang has been requested but it is invalid, let's fall back to the default one.
				$this->curlang = $this->model->get_language( sanitize_key( $this->options['default_lang'] ) );
			}
		}

		if ( ! empty( $this->curlang ) ) {
			/** This action is documented in frontend/choose-lang.php */
			do_action( 'lmat_language_defined', $this->curlang->slug, $this->curlang );
		} else {
			/** This action is documented in include/class-linguator.php */
			do_action( 'lmat_no_language_defined' ); // To load overridden textdomains.
		}

		return $result;
	}
}
