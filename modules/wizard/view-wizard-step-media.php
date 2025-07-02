<?php
/**
 * Displays the wizard media step
 *
 * @package Linguator
 *
 * @since 2.7
 *
 * @var Options $options Linguator's options.
 */

use WP_Syntex\Linguator\Options\Options;

defined( 'ABSPATH' ) || exit;

$help_screenshot = '/modules/wizard/images/media-screen' . ( is_rtl() ? '-rtl' : '' ) . '.png';

?>
<h2><?php esc_html_e( 'Media', 'linguator' ); ?></h2>
<p>
	<?php esc_html_e( 'Linguator allows you to translate the text attached to your media, for example the title, the alternative text, the caption, or the description.', 'linguator' ); ?>
	<?php esc_html_e( 'When you translate a media, the file is not duplicated on your disk, however you will see one entry per language in the media library.', 'linguator' ); ?>
	<?php esc_html_e( 'When you want to insert media in a post, only the media in the language of the current post will be displayed.', 'linguator' ); ?>
</p>
<p>
	<?php esc_html_e( 'You must activate media translation if you want to translate the title, the alternative text, the caption, or the description. Otherwise you can safely deactivate it.', 'linguator' ); ?>
</p>
<ul class="lmat-wizard-services">
	<li class="lmat-wizard-service-item">
		<div class="lmat-wizard-service-enable">
			<span class="lmat-wizard-service-toggle">
				<input
					id="lmat-wizard-service-media"
					type="checkbox"
					name="media_support"
					value="yes" <?php checked( $options['media_support'] ); ?>
				/>
				<label for="lmat-wizard-service-media" />
			</span>
		</div>
		<div class="lmat-wizard-service-description">
			<p>
				<?php esc_html_e( 'Allow Linguator to translate media', 'linguator' ); ?>
			</p>
		</div>
	</li>
	<li class="lmat-wizard-service-item">
		<div class="lmat-wizard-service-example">
			<p>
				<input id="slide-toggle" type="checkbox" checked="checked">
				<label for="slide-toggle" class="button button-primary button-small">
					<span class="dashicons dashicons-visibility"></span><?php esc_html_e( 'Help', 'linguator' ); ?>
				</label>
				<span id="screenshot">
					<img src="<?php echo esc_url( plugins_url( $help_screenshot, LINGUATOR_FILE ) ); ?>" />
				</span>
			</p>
		</div>
	</li>
</ul>
