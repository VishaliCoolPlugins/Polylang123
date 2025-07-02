<?php
/**
 * @package Linguator
 */

/**
 * Settings class for licenses
 *
 * @since 1.9
 */
class LMAT_Settings_Licenses extends LMAT_Settings_Module {
	/**
	 * Stores the display order priority.
	 *
	 * @var int
	 */
	public $priority = 100;

	/**
	 * Stores an array of objects allowing to manage a license.
	 *
	 * @var LMAT_License[]
	 */
	protected $items;

	/**
	 * Constructor
	 *
	 * @since 1.9
	 *
	 * @param object $linguator linguator object
	 */
	public function __construct( &$linguator ) {
		parent::__construct(
			$linguator,
			array(
				'module'      => 'licenses',
				'title'       => __( 'License keys', 'linguator' ),
				'description' => __( 'Manage licenses for Linguator Pro and add-ons.', 'linguator' ),
			)
		);

		$this->buttons['cancel'] = sprintf( '<button type="button" class="button button-secondary cancel">%s</button>', __( 'Close', 'linguator' ) );

		$this->items = apply_filters( 'lmat_settings_licenses', array() );

		add_action( 'wp_ajax_lmat_deactivate_license', array( $this, 'deactivate_license' ) );
	}

	/**
	 * Tells if the module is active
	 *
	 * @since 1.9
	 *
	 * @return bool
	 */
	public function is_active() {
		return ! empty( $this->items );
	}

	/**
	 * Displays the settings form
	 *
	 * @since 1.9
	 */
	protected function form() {
		if ( ! empty( $this->items ) ) { ?>
			<table id="lmat-licenses-table" class="form-table lmat-table-top">
				<?php
				foreach ( $this->items as $item ) {
					echo $this->get_row( $item ); // phpcs:ignore WordPress.Security.EscapeOutput
				}
				?>
			</table>
			<?php
		}
	}

	/**
	 * Get the html for a row (one per license key) for display.
	 *
	 * @since 1.9
	 *
	 * @param LMAT_License $item Object allowing to manage a license.
	 * @return string
	 */
	protected function get_row( $item ) {
		return $item->get_form_field();
	}

	/**
	 * Ajax method to save the license keys and activate the licenses at the same time
	 * Overrides parent's method
	 *
	 * @since 1.9
	 */
	public function save_options() {
		check_ajax_referer( 'lmat_options', '_lmat_nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1 );
		}

		if ( isset( $_POST['module'] ) && $this->module === $_POST['module'] && ! empty( $_POST['licenses'] ) ) {
			$x = new WP_Ajax_Response();
			foreach ( $this->items as $item ) {
				if ( ! empty( $_POST['licenses'][ $item->id ] ) ) {
					$updated_item = $item->activate_license( sanitize_key( $_POST['licenses'][ $item->id ] ) );
					$x->Add( array( 'what' => 'license-update', 'data' => $item->id, 'supplemental' => array( 'html' => $this->get_row( $updated_item ) ) ) );
				}
			}

			// Updated message
			lmat_add_notice( new WP_Error( 'settings_updated', __( 'Settings saved.', 'linguator' ), 'success' ) );
			ob_start();
			settings_errors( 'linguator' );
			$x->Add( array( 'what' => 'success', 'data' => ob_get_clean() ) );
			$x->send();
		}
	}

	/**
	 * Ajax method to deactivate a license
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function deactivate_license() {
		check_ajax_referer( 'lmat_options', '_lmat_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1 );
		}

		if ( ! isset( $_POST['id'] ) ) {
			wp_die( 0 );
		}

		$id = substr( sanitize_text_field( wp_unslash( $_POST['id'] ) ), 11 );
		wp_send_json(
			array(
				'id'   => $id,
				'html' => $this->get_row( $this->items[ $id ]->deactivate_license() ),
			)
		);
	}
}
