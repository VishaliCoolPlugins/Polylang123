<?php
/**
 * Displays the wizard unstranslated content step
 *
 * @package Linguator
 *
 * @since 2.7
 *
 * @var LMAT_Model $model `LMAT_Model` instance.
 */

defined( 'ABSPATH' ) || exit;

$languages_list = $model->languages->get_list();
?>
<h2><?php esc_html_e( 'Content without language', 'linguator' ); ?></h2>
<p>
	<?php esc_html_e( 'There are posts, pages, categories or tags without language.', 'linguator' ); ?><br />
	<?php esc_html_e( 'For your site to work correctly, you need to assign a language to all your contents.', 'linguator' ); ?><br />
	<?php esc_html_e( 'The selected language below will be applied to all your content without an assigned language.', 'linguator' ); ?>
</p>
<div class="form-field">
	<label for="lang_list"><?php esc_html_e( 'Choose the language to be assigned', 'linguator' ); ?></label>
	<select name="language" id="lang_list">
		<?php
		foreach ( $languages_list as $lg ) {
			printf(
				'<option value="%1$s" data-flag-html="%3$s" data-language-name="%2$s"%4$s>%2$s - %1$s</option>' . "\n",
				esc_attr( $lg->locale ),
				esc_html( $lg->name ),
				esc_html( $lg->flag ),
				$lg->is_default ? ' selected="selected"' : ''
			);
		}
		?>
	</select>
</div>
