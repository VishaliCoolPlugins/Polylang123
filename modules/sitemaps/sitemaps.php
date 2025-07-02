<?php
/**
 * @package Linguator
 */

/**
 * Handles the core sitemaps for sites using a single domain.
 *
 * @since 2.8
 */
class LMAT_Sitemaps extends LMAT_Abstract_Sitemaps {
	/**
	 * @var LMAT_Links_Model
	 */
	protected $links_model;

	/**
	 * @var LMAT_Model
	 */
	protected $model;

	/**
	 * Stores the plugin options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @since 2.8
	 *
	 * @param object $linguator Main Linguator object.
	 */
	public function __construct( &$linguator ) {
		$this->links_model = &$linguator->links_model;
		$this->model = &$linguator->model;
		$this->options = &$linguator->options;
	}

	/**
	 * Setups actions and filters.
	 *
	 * @since 2.8
	 *
	 * @return void
	 */
	public function init() {
		parent::init();

		add_filter( 'lmat_set_language_from_query', array( $this, 'set_language_from_query' ), 10, 2 );
		add_filter( 'rewrite_rules_array', array( $this, 'rewrite_rules' ) );
		add_filter( 'wp_sitemaps_add_provider', array( $this, 'replace_provider' ) );
	}

	/**
	 * Assigns the current language to the default language when the sitemap url
	 * doesn't include any language.
	 *
	 * @since 2.8
	 *
	 * @param string|bool $lang  Current language code, false if not set yet.
	 * @param WP_Query    $query Main WP query object.
	 * @return string|bool
	 */
	public function set_language_from_query( $lang, $query ) {
		if ( isset( $query->query['sitemap'] ) && empty( $query->query['lang'] ) ) {
			$lang = $this->options['default_lang'];
		}
		return $lang;
	}

	/**
	 * Filters the sitemaps rewrite rules to take the languages into account.
	 *
	 * @since 2.8
	 *
	 * @param string[] $rules Rewrite rules.
	 * @return string[] Modified rewrite rules.
	 */
	public function rewrite_rules( $rules ) {
		global $wp_rewrite;

		$languages = $this->model->get_languages_list(
			array(
				'fields'       => 'slug',
				'hide_default' => $this->options['hide_default'],
			)
		);

		if ( empty( $languages ) ) {
			return $rules;
		}

		$slug = $wp_rewrite->root . ( $this->options['rewrite'] ? '^' : '^language/' ) . '(' . implode( '|', $languages ) . ')/';

		$newrules = array();

		foreach ( $rules as $key => $rule ) {
			if ( false !== strpos( $rule, 'sitemap=$matches[1]' ) ) {
				$newrules[ str_replace( '^wp-sitemap', $slug . 'wp-sitemap', $key ) ] = str_replace(
					array( '[8]', '[7]', '[6]', '[5]', '[4]', '[3]', '[2]', '[1]', '?' ),
					array( '[9]', '[8]', '[7]', '[6]', '[5]', '[4]', '[3]', '[2]', '?lang=$matches[1]&' ),
					$rule
				); // Should be enough!
			}

			$newrules[ $key ] = $rule;
		}
		return $newrules;
	}

	/**
	 * Replaces a sitemap provider by our decorator.
	 *
	 * @since 2.8
	 *
	 * @param WP_Sitemaps_Provider $provider Instance of a WP_Sitemaps_Provider.
	 * @return WP_Sitemaps_Provider
	 */
	public function replace_provider( $provider ) {
		if ( $provider instanceof WP_Sitemaps_Provider ) {
			$provider = new LMAT_Multilingual_Sitemaps_Provider( $provider, $this->links_model );
		}
		return $provider;
	}
}
