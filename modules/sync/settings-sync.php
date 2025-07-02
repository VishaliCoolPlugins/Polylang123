<?php
/**
 * @package Linguator
 */

/**
 * Settings class for synchronization settings management
 *
 * @since 1.8
 */
class LMAT_Settings_Sync extends LMAT_Settings_Module {
	/**
	 * Stores the display order priority.
	 *
	 * @var int
	 */
	public $priority = 50;

	/**
	 * Constructor
	 *
	 * @since 1.8
	 *
	 * @param object $linguator The linguator object.
	 */
	public function __construct( &$linguator ) {
		parent::__construct(
			$linguator,
			array(
				'module'      => 'sync',
				'title'       => __( 'Synchronization', 'linguator' ),
				'description' => __( 'The synchronization options allow to maintain exact same values (or translations in the case of taxonomies and page parent) of meta content between the translations of a post or page.', 'linguator' ),
			)
		);
	}

	/**
	 * Deactivates the module
	 *
	 * @since 1.8
	 */
	public function deactivate() {
		$this->options['sync'] = array();
	}

	/**
	 * Displays the settings form
	 *
	 * @since 1.8
	 */
	protected function form() {
		?>
		<ul class="lmat-inline-block-list">
			<?php
			foreach ( self::list_metas_to_sync() as $key => $str ) {
				printf(
					'<li><label><input name="sync[%s]" type="checkbox" value="1" %s /> %s</label></li>',
					esc_attr( $key ),
					checked( in_array( $key, $this->options['sync'] ), true, false ),
					esc_html( $str )
				);
			}
			?>
		</ul>
		<?php
	}

	/**
	 * Prepare the received data before saving.
	 *
	 * @since 3.7
	 *
	 * @param array $options Raw values to save.
	 * @return array
	 */
	protected function prepare_raw_data( array $options ): array {
		// Take care to return only validated options.
		return array( 'sync' => empty( $options['sync'] ) ? array() : array_keys( $options['sync'], 1 ) );
	}

	/**
	 * Get the row actions.
	 *
	 * @since 1.8
	 *
	 * @return string[] Row actions.
	 */
	protected function get_actions() {
		return empty( $this->options['sync'] ) ? array( 'configure' ) : array( 'configure', 'deactivate' );
	}

	/**
	 * Get the list of synchronization settings.
	 *
	 * @since 1.0
	 *
	 * @return string[] Array synchronization options.
	 *
	 * @phpstan-return non-empty-array<non-falsy-string, string>
	 */
	public static function list_metas_to_sync() {
		return array(
			'taxonomies'        => __( 'Taxonomies', 'linguator' ),
			'post_meta'         => __( 'Custom fields', 'linguator' ),
			'comment_status'    => __( 'Comment status', 'linguator' ),
			'ping_status'       => __( 'Ping status', 'linguator' ),
			'sticky_posts'      => __( 'Sticky posts', 'linguator' ),
			'post_date'         => __( 'Published date', 'linguator' ),
			'post_format'       => __( 'Post format', 'linguator' ),
			'post_parent'       => __( 'Page parent', 'linguator' ),
			'_wp_page_template' => __( 'Page template', 'linguator' ),
			'menu_order'        => __( 'Page order', 'linguator' ),
			'_thumbnail_id'     => __( 'Featured image', 'linguator' ),
		);
	}
}
