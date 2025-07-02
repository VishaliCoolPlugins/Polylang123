<?php
/**
 * @package Linguator
 */

use WP_Syntex\Linguator\Options\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Sets the taxonomies languages and translations model up.
 *
 * @since 1.8
 *
 * @phpstan-import-type DBInfoWithType from LMAT_Translatable_Object_With_Types_Interface
 */
class LMAT_Translated_Term extends LMAT_Translated_Object implements LMAT_Translatable_Object_With_Types_Interface {
	use LMAT_Translatable_Object_With_Types_Trait;

	/**
	 * Taxonomy name for the languages.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected $tax_language = 'term_language';

	/**
	 * Object type to use when registering the taxonomy.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected $object_type = 'term';

	/**
	 * Identifier that must be unique for each type of content.
	 * Also used when checking capabilities.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected $type = 'term';

	/**
	 * Identifier for each type of content to used for cache type.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected $cache_type = 'terms';


	/**
	 * Taxonomy name for the translation groups.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	protected $tax_translations = 'term_translations';

	/**
	 * Constructor.
	 *
	 * @since 1.8
	 *
	 * @param LMAT_Model $model Instance of `LMAT_Model`.
	 */
	public function __construct( LMAT_Model $model ) {
		parent::__construct( $model );

		// Keep hooks in constructor for backward compatibility.
		$this->init();
	}

	/**
	 * Adds hooks.
	 *
	 * @since 3.4
	 *
	 * @return static
	 */
	public function init() {
		add_filter( 'get_terms', array( $this, '_prime_terms_cache' ), 10, 2 );
		add_action( 'clean_term_cache', array( $this, 'clean_term_cache' ) );
		return parent::init();
	}

	/**
	 * Stores the term's language into the database.
	 *
	 * @since 0.6
	 * @since 3.4 Renamed the parameter $term_id into $id.
	 *
	 * @param int                     $id   Term ID.
	 * @param LMAT_Language|string|int $lang Language (object, slug, or term ID).
	 * @return bool True when successfully assigned. False otherwise (or if the given language is already assigned to
	 *              the object).
	 */
	public function set_language( $id, $lang ) {
		if ( ! parent::set_language( $id, $lang ) ) {
			return false;
		}

		$id = $this->sanitize_int_id( $id );

		// Add translation group for correct WXR export.
		$translations = $this->get_translations( $id );

		if ( ! empty( $translations ) ) {
			$translations = array_diff( $translations, array( $id ) );
		}

		$this->save_translations( $id, $translations );

		return true;
	}

	/**
	 * Returns the language of a term.
	 *
	 * @since 0.1
	 * @since 3.4 Renamed the parameter $value into $id.
	 * @since 3.4 Deprecated to retrieve the language by term slug + taxonomy anymore.
	 *
	 * @param int $id Term ID.
	 * @return LMAT_Language|false A `LMAT_Language` object. `false` if no language is associated to that term or if the
	 *                            ID is invalid.
	 */
	public function get_language( $id ) {
		if ( func_num_args() > 1 ) {
			// Backward compatibility.
			_deprecated_argument( __METHOD__ . '()', '3.4' );

			$term = get_term_by( 'slug', $id, func_get_arg( 1 ) ); // @phpstan-ignore-line
			$id   = $term instanceof WP_Term ? $term->term_id : 0;
		}

		return parent::get_language( $id );
	}

	/**
	 * Deletes a translation of a term.
	 *
	 * @since 0.5
	 *
	 * @param int $id Term ID.
	 * @return void
	 */
	public function delete_translation( $id ) {
		global $wpdb;

		$id = $this->sanitize_int_id( $id );

		if ( empty( $id ) ) {
			return;
		}

		$slug = array_search( $id, $this->get_translations( $id ) ); // In case some plugin stores the same value with different key.

		parent::delete_translation( $id );
		wp_delete_object_term_relationships( $id, $this->tax_translations );

		if ( doing_action( 'pre_delete_term' ) ) {
			return;
		}

		if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( * ) FROM $wpdb->terms WHERE term_id = %d;", $id ) ) ) {
			return;
		}

		// Always keep a group for terms to allow relationships remap when importing from a WXR file.
		$group        = uniqid( 'lmat_' );
		$translations = array( $slug => $id );
		wp_insert_term( $group, $this->tax_translations, array( 'description' => maybe_serialize( $translations ) ) );
		wp_set_object_terms( $id, $group, $this->tax_translations );
	}

	/**
	 * Returns object types (taxonomy names) that need to be translated.
	 * The taxonomies list is cached for better performance.
	 * The method waits for 'after_setup_theme' to apply the cache to allow themes adding the filter in functions.php.
	 *
	 * @since 3.4
	 *
	 * @param bool $filter True if we should return only valid registered object types.
	 * @return string[] Object type names for which Linguator manages languages.
	 *
	 * @phpstan-return array<non-empty-string, non-empty-string>
	 */
	public function get_translated_object_types( $filter = true ) {
		$taxonomies = $this->cache->get( 'taxonomies' );

		if ( false === $taxonomies ) {
			$taxonomies = array( 'category' => 'category', 'post_tag' => 'post_tag' );

			if ( ! empty( $this->options['taxonomies'] ) ) {
				$taxonomies = array_merge( $taxonomies, array_combine( $this->options['taxonomies'], $this->options['taxonomies'] ) );
			}

			/**
			 * Filters the list of taxonomies available for translation.
			 * The default are taxonomies which have the parameter ‘public’ set to true.
			 * The filter must be added soon in the WordPress loading process:
			 * in a function hooked to ‘plugins_loaded’ or directly in functions.php for themes.
			 *
			 * @since 0.8
			 *
			 * @param string[] $taxonomies  List of taxonomy names (as array keys and values).
			 * @param bool     $is_settings True when displaying the list of custom taxonomies in Linguator settings.
			 */
			$taxonomies = (array) apply_filters( 'lmat_get_taxonomies', $taxonomies, false );

			if ( did_action( 'after_setup_theme' ) && ! doing_action( 'switch_blog' ) ) {
				$this->cache->set( 'taxonomies', $taxonomies );
			}
		}

		/** @var array<non-empty-string, non-empty-string> $taxonomies */
		return $filter ? array_intersect( $taxonomies, get_taxonomies() ) : $taxonomies;
	}

	/**
	 * Caches the language and translations when terms are queried by get_terms().
	 *
	 * @since 1.2
	 *
	 * @param WP_Term[]|int[] $terms      Queried terms.
	 * @param string[]        $taxonomies Queried taxonomies.
	 * @return WP_Term[]|int[] Unmodified $terms.
	 *
	 * @phpstan-param array<WP_Term|positive-int> $terms
	 * @phpstan-param array<non-empty-string> $taxonomies
	 * @phpstan-return array<WP_Term|positive-int>
	 */
	public function _prime_terms_cache( $terms, $taxonomies ) {
		$ids = array();

		if ( is_array( $terms ) && $this->is_translated_object_type( $taxonomies ) ) {
			foreach ( $terms as $term ) {
				$ids[] = is_object( $term ) ? $term->term_id : (int) $term;
			}
		}

		if ( ! empty( $ids ) ) {
			update_object_term_cache( array_unique( $ids ), 'term' ); // Adds language and translation of terms to cache.
		}
		return $terms;
	}

	/**
	 * When the term cache is cleaned, cleans the object term cache too.
	 *
	 * @since 2.0
	 *
	 * @param int[] $ids An array of term IDs.
	 * @return void
	 *
	 * @phpstan-param array<positive-int> $ids
	 */
	public function clean_term_cache( $ids ) {
		clean_object_term_cache( $this->sanitize_int_ids_list( $ids ), 'term' );
	}

	/**
	 * Tells whether a translation term must be updated.
	 *
	 * @since 2.3
	 *
	 * @param int   $id           Term ID.
	 * @param int[] $translations An associative array of translations with language code as key and translation ID as
	 *                            value. Make sure to sanitize this.
	 * @return bool
	 *
	 * @phpstan-param array<non-empty-string, positive-int> $translations
	 */
	protected function should_update_translation_group( $id, $translations ) {
		// Don't do anything if no translations have been added to the group.
		$old_translations = $this->get_translations( $id );
		if ( count( $translations ) > 1 && ! empty( array_diff_assoc( $translations, $old_translations ) ) ) {
			return true;
		}

		// But we need a translation group for terms to allow relationships remap when importing from a WXR file
		$term = $this->get_object_term( $id, $this->tax_translations );
		return empty( $term ) || ! empty( array_diff_assoc( $translations, $old_translations ) );
	}

	/**
	 * Assigns a language to terms in mass.
	 *
	 * @since 1.2
	 * @since 3.4 Moved from LMAT_Admin_Model class.
	 *
	 * @param int[]        $ids  Array of post ids or term ids.
	 * @param LMAT_Language $lang Language to assign to the posts or terms.
	 * @return void
	 */
	public function set_language_in_mass( $ids, $lang ) {
		parent::set_language_in_mass( $ids, $lang );

		$translations = array();

		foreach ( $ids as $id ) {
			$translations[] = array( $lang->slug => $id );
		}

		if ( ! empty( $translations ) ) {
			$this->set_translation_in_mass( $translations );
		}
	}

	/**
	 * Returns the description to use for the "language properties" in the REST API.
	 *
	 * @since 3.7
	 * @see WP_Syntex\Linguator\REST\V2\Languages::get_item_schema()
	 *
	 * @return string
	 */
	public function get_rest_description(): string {
		return __( 'Language taxonomy properties for terms.', 'linguator' );
	}

	/**
	 * Returns database-related information that can be used in some of this class methods.
	 * These are specific to the table containing the objects.
	 *
	 * @see LMAT_Translatable_Object::join_clause()
	 * @see LMAT_Translatable_Object::get_raw_objects_with_no_lang()
	 *
	 * @since 3.4.3
	 *
	 * @return string[] {
	 *     @type string $table         Name of the table.
	 *     @type string $id_column     Name of the column containing the object's ID.
	 *     @type string $type_column   Name of the column containing the object's type.
	 *     @type string $default_alias Default alias corresponding to the object's table.
	 * }
	 * @phpstan-return DBInfoWithType
	 */
	protected function get_db_infos() {
		return array(
			'table'         => $GLOBALS['wpdb']->term_taxonomy,
			'id_column'     => 'term_id',
			'type_column'   => 'taxonomy',
			'default_alias' => 't',
		);
	}

	/**
	 * Wraps `wp_insert_term` with language feature.
	 *
	 * @since 3.7
	 *
	 * @param string       $term     The term name to add.
	 * @param string       $taxonomy The taxonomy to which to add the term.
	 * @param LMAT_Language $language The term language.
	 * @param array        $args {
	 *     Optional. Array of arguments for inserting a term.
	 *
	 *     @type string   $alias_of     Slug of the term to make this term an alias of.
	 *                                  Default empty string. Accepts a term slug.
	 *     @type string   $description  The term description. Default empty string.
	 *     @type int      $parent       The id of the parent term. Default 0.
	 *     @type string   $slug         The term slug to use. Default empty string.
	 *     @type string[] $translations The translation group to assign to the term with language slug as keys and `term_id` as values.
	 * }
	 * @return array|WP_Error {
	 *     An array of the new term data, `WP_Error` otherwise.
	 *
	 *     @type int        $term_id          The new term ID.
	 *     @type int|string $term_taxonomy_id The new term taxonomy ID. Can be a numeric string.
	 * }
	 */
	public function insert( string $term, string $taxonomy, LMAT_Language $language, $args = array() ) {
		$parent = $args['parent'] ?? 0;
		$this->toggle_inserted_term_filters( $language, $parent );
		$term = wp_insert_term( $term, $taxonomy, $args );
		$this->toggle_inserted_term_filters( $language, $parent );

		if ( is_wp_error( $term ) ) {
			// Something went wrong!
			return $term;
		}

		$this->set_language( (int) $term['term_id'], $language );

		if ( ! empty( $args['translations'] ) ) {
			$this->save_translations( (int) $term['term_id'], $args['translations'] );
		}

		return $term;
	}

	/**
	 * Wraps `wp_update_term` with language feature.
	 *
	 * @since 3.7
	 *
	 * @param int   $term_id The ID of the term.
	 * @param array $args {
	 *     Optional. Array of arguments for updating a term.
	 *
	 *     @type string       $alias_of     Slug of the term to make this term an alias of.
	 *                                      Default empty string. Accepts a term slug.
	 *     @type string       $description  The term description. Default empty string.
	 *     @type int          $parent       The id of the parent term. Default 0.
	 *     @type string       $slug         The term slug to use. Default empty string.
	 *     @type LMAT_Language $lang         The term language object.
	 *     @type string[]     $translations The translation group to assign to the term with language slug as keys and `term_id` as values.
	 * }
	 * @return array|WP_Error An array containing the `term_id` and `term_taxonomy_id`,
	 *                        WP_Error otherwise.
	 */
	public function update( int $term_id, array $args = array() ) {
		$term = get_term( $term_id );
		if ( ! $term instanceof WP_Term ) {
			return new WP_Error( 'invalid_term', __( 'Empty Term.', 'linguator' ) );
		}

		/** @var LMAT_Language $language */
		$language = $this->get_language( $term_id );
		if ( ! empty( $args['lang'] ) ) {
			$language = $this->languages->get( $args['lang'] );
			if ( ! $language instanceof LMAT_Language ) {
				return new WP_Error( 'invalid_language', __( 'Please provide a valid language.', 'linguator' ) );
			}

			$this->set_language( $term_id, $language );
		}

		$parent = $args['parent'] ?? $term->parent;
		$this->toggle_inserted_term_filters( $language, $parent );
		$term = wp_update_term( $term->term_id, $term->taxonomy, $args );
		$this->toggle_inserted_term_filters( $language, $parent );

		if ( is_wp_error( $term ) ) {
			// Something went wrong!
			return $term;
		}

		if ( ! empty( $args['translations'] ) ) {
			$this->save_translations( $term_id, $args['translations'] );
		}

		return $term;
	}

	/**
	 * Toggles Linguator term slug filters management.
	 * Must be used before and after any term slug modification or insertion.
	 *
	 * @since 3.7
	 *
	 * @param LMAT_Language $language The language to use.
	 * @param int          $parent   The parent term id to use.
	 * @return void
	 */
	private function toggle_inserted_term_filters( LMAT_Language $language, int $parent ): void {
		static $callbacks = array();
		if ( isset( $callbacks[ $language->slug ], $callbacks[ (string) $parent ] ) ) {
			// Clean up!
			remove_filter( 'lmat_inserted_term_language', $callbacks[ $language->slug ] );
			remove_filter( 'lmat_inserted_term_parent', $callbacks[ (string) $parent ] );
			unset( $callbacks[ $language->slug ], $callbacks[ (string) $parent ] );
			return;
		}

		$callbacks[ $language->slug ] = function () use ( $language ) {
			return $language;
		};
		$callbacks[ (string) $parent ] = function () use ( $parent ) {
			return $parent;
		};

		// Set term parent and language for suffixed slugs.
		add_filter( 'lmat_inserted_term_language', $callbacks[ $language->slug ] );
		add_filter( 'lmat_inserted_term_parent', $callbacks[ (string) $parent ] );
	}
}
