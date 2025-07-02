<?php
/**
 * @package Linguator
 */

/**
 * Settings class for browser language preference detection
 *
 * @since 1.8
 */
class LMAT_Settings_Browser extends LMAT_Settings_Module {
	/**
	 * Stores the display order priority.
	 *
	 * @var int
	 */
	public $priority = 20;

	/**
	 * Constructor
	 *
	 * @since 1.8
	 *
	 * @param object $linguator linguator object
	 */
	public function __construct( &$linguator ) {
		// Needed for `$this->is_available()`, which is used before calling the parent's constructor.
		$this->options = &$linguator->options;

		parent::__construct(
			$linguator,
			array(
				'module'        => 'browser',
				'title'         => __( 'Detect browser language', 'linguator' ),
				'description'   => __( 'When the front page is visited, redirects to itself in the browser preferred language. As this doesn\'t work if it is cached, Linguator will attempt to disable the front page cache for known cache plugins.', 'linguator' ),
				'active_option' => $this->is_available() ? 'browser' : 'none',
			)
		);

		if ( ! class_exists( 'LMAT_Xdata_Domain', true ) ) {
			add_action( 'admin_print_footer_scripts', array( $this, 'print_js' ) );
		}
	}

	/**
	 * Tells if the option is available
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	protected function is_available() {
		return ( 3 > $this->options['force_lang'] ) || class_exists( 'LMAT_Xdata_Domain', true );
	}

	/**
	 * Tells if the module is active
	 *
	 * @since 1.8
	 *
	 * @return bool
	 */
	public function is_active() {
		return $this->is_available() ? parent::is_active() : false;
	}

	/**
	 * Displays the javascript to handle dynamically the change in url modifications
	 * as the preferred browser language is not used when the language is set from different domains
	 *
	 * @since 1.8
	 *
	 * @return void
	 */
	public function print_js() {
		wp_enqueue_script( 'jquery' );

		if ( parent::is_active() && 3 > $this->options['force_lang'] ) {
			$func = 'removeClass( "inactive" ).addClass( "active" )';
			$link = sprintf( '<span class="deactivate">%s</span>', $this->action_links['deactivate'] );
		}
		else {
			$func = 'removeClass( "active" ).addClass( "inactive" )';
			$link = sprintf( '<span class="activate">%s</span>', $this->action_links['activate'] );
		}

		$deactivated = sprintf( '<span class="deactivated">%s</span>', $this->action_links['deactivated'] );

		?>
		<script>
			jQuery(
				function( $ ){
					$( "input[name='force_lang']" ).on( 'change', function() {
						var value = $( this ).val();
						if ( 3 > value ) {
							$( "#lmat-module-browser" ).<?php echo $func; // phpcs:ignore WordPress.Security.EscapeOutput ?>.children( "td" ).children( ".row-actions" ).html( '<?php echo $link; // phpcs:ignore WordPress.Security.EscapeOutput ?>' );
						}
						else {
							$( "#lmat-module-browser" ).removeClass( "active" ).addClass( "inactive" ).children( "td" ).children( ".row-actions" ).html( '<?php echo $deactivated; // phpcs:ignore WordPress.Security.EscapeOutput ?>' );
						}
					} );
				}
			);
		</script>
		<?php
	}
}
