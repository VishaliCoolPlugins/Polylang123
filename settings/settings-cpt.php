<?php
/**
 * @package Linguator
 */

/**
 * Settings class for custom post types and taxonomies language and translation management
 *
 * @since 1.8
 */
class LMAT_Settings_CPT extends LMAT_Settings_Module {
	/**
	 * Stores the display order priority.
	 *
	 * @var int
	 */
	public $priority = 40;

	/**
	 * The list of post types to show in the form.
	 *
	 * @var string[]
	 */
	private $post_types;

	/**
	 * The list of post types to disable in the form.
	 *
	 * @var string[]
	 */
	private $disabled_post_types;

	/**
	 * The list of taxonomies to show in the form.
	 *
	 * @var string[]
	 */
	private $taxonomies;

	/**
	 * The list of taxonomies to disable in the form.
	 *
	 * @var string[]
	 */
	private $disabled_taxonomies;

	/**
	 * Constructor.
	 *
	 * @since 1.8
	 *
	 * @param object $linguator The Linguator object.
	 */
	public function __construct( &$linguator ) {
		parent::__construct(
			$linguator,
			array(
				'module'      => 'cpt',
				'title'       => __( 'Custom post types and Taxonomies', 'linguator' ),
				'description' => __( 'Activate languages and translations management for the custom post types and the taxonomies.', 'linguator' ),
			)
		);

		$public_post_types = get_post_types( array( 'public' => true, '_builtin' => false ) );
		/** This filter is documented in include/model.php */
		$this->post_types = array_unique( apply_filters( 'lmat_get_post_types', $public_post_types, true ) );

		/** This filter is documented in include/model.php */
		$programmatically_active_post_types = array_unique( apply_filters( 'lmat_get_post_types', array(), false ) );
		$this->disabled_post_types = array_intersect( $programmatically_active_post_types, $this->post_types );

		$public_taxonomies = get_taxonomies( array( 'public' => true, '_builtin' => false ) );
		$public_taxonomies = array_diff( $public_taxonomies, get_taxonomies( array( '_lmat' => true ) ) );
		/** This filter is documented in include/model.php */
		$this->taxonomies = array_unique( apply_filters( 'lmat_get_taxonomies', $public_taxonomies, true ) );

		/** This filter is documented in include/model.php */
		$programmatically_active_taxonomies = array_unique( apply_filters( 'lmat_get_taxonomies', array(), false ) );
		$this->disabled_taxonomies = array_intersect( $programmatically_active_taxonomies, $this->taxonomies );
	}

	/**
	 * Tells if the module is active
	 *
	 * @since 1.8
	 *
	 * @return bool
	 */
	public function is_active() {
		return ! empty( $this->post_types ) || ! empty( $this->taxonomies );
	}

	/**
	 * Displays the settings form
	 *
	 * @since 1.8
	 */
	protected function form() {
		if ( ! empty( $this->post_types ) ) {?>
			<h4><?php esc_html_e( 'Custom post types', 'linguator' ); ?></h4>
			<ul class="lmat-inline-block-list">
				<?php
				foreach ( $this->post_types as $post_type ) {
					$pt = get_post_type_object( $post_type );
					if ( ! empty( $pt ) ) {
						$disabled = in_array( $post_type, $this->disabled_post_types );
						printf(
							'<li><label><input name="post_types[%s]" type="checkbox" value="1" %s %s/> %s</label></li>',
							esc_attr( $post_type ),
							checked( $disabled || in_array( $post_type, $this->options['post_types'], true ), true, false ),
							disabled( $disabled, true, false ),
							esc_html(
								sprintf(
									/* translators: 1 is a post type or taxonomy label, 2 is a post type or taxonomy key. */
									_x( '%1$s (%2$s)', 'content type setting choice', 'linguator' ),
									$pt->labels->name,
									$pt->name
								)
							)
						);
					}
				}
				?>
			</ul>
			<p class="description"><?php esc_html_e( 'Activate languages and translations for custom post types.', 'linguator' ); ?></p>
			<?php
		}

		if ( ! empty( $this->taxonomies ) ) {
			?>
			<h4><?php esc_html_e( 'Custom taxonomies', 'linguator' ); ?></h4>
			<ul class="lmat-inline-block-list">
				<?php
				foreach ( $this->taxonomies as $taxonomy ) {
					$tax = get_taxonomy( $taxonomy );
					if ( ! empty( $tax ) ) {
						$disabled = in_array( $taxonomy, $this->disabled_taxonomies );
						printf(
							'<li><label><input name="taxonomies[%s]" type="checkbox" value="1" %s %s/> %s</label></li>',
							esc_attr( $taxonomy ),
							checked( $disabled || in_array( $taxonomy, $this->options['taxonomies'], true ), true, false ),
							disabled( $disabled, true, false ),
							esc_html(
								sprintf(
									/* translators: 1 is a post type or taxonomy label, 2 is a post type or taxonomy key. */
									_x( '%1$s (%2$s)', 'content type setting choice', 'linguator' ),
									$tax->labels->name,
									$tax->name
								)
							)
						);
					}
				}
				?>
			</ul>
			<p class="description"><?php esc_html_e( 'Activate languages and translations for custom taxonomies.', 'linguator' ); ?></p>
			<?php
		}
	}

	/**
	 * Prepares the received data before saving.
	 *
	 * @since 3.7
	 *
	 * @param array $options Raw values to save.
	 * @return array
	 */
	protected function prepare_raw_data( array $options ): array {
		$newoptions = array();

		foreach ( array( 'post_types', 'taxonomies' ) as $key ) {
			$newoptions[ $key ] = empty( $options[ $key ] ) ? array() : array_keys( $options[ $key ], 1 );
		}

		return $newoptions; // Take care to return only validated options.
	}
}
