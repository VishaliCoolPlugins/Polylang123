<?php
/**
 * Displays the wizard languages step
 *
 * @package Linguator
 *
 * @since 2.7
 *
 * @var LMAT_Model $model   `LMAT_Model` instance.
 */

defined( 'ABSPATH' ) || exit;

$existing_languages = $model->languages->get_list();
$default_language = $model->languages->get_default();
$languages_list = array_diff_key(
	LMAT_Settings::get_predefined_languages(),
	wp_list_pluck( $existing_languages, 'locale', 'locale' )
);
?>
<div id="language-fields"></div>
<p class="languages-setup">
	<?php esc_html_e( 'This wizard will help you configure your Linguator settings, and get you started quickly with your multilingual website.', 'linguator' ); ?>
</p>
<p class="languages-setup">
	<?php esc_html_e( 'First we are going to define the languages that you will use on your website.', 'linguator' ); ?>
</p>
<h2><?php esc_html_e( 'Languages', 'linguator' ); ?></h2>
<div id="messages">
</div>
<div class="form-field">
	<label for="lang_list"><?php esc_html_e( 'Select a language to be added', 'linguator' ); ?></label>
	<div class="select-language-field">
		<select name="lang_list" id="lang_list">
			<option value=""></option>
			<?php
			foreach ( $languages_list as $language ) {
				printf(
					'<option value="%1$s" data-flag-html="%3$s" data-language-name="%2$s" >%2$s - %1$s</option>' . "\n",
					esc_attr( $language['locale'] ),
					esc_attr( $language['name'] ),
					esc_attr( LMAT_Language::get_predefined_flag( $language['flag'] ) )
				);
			}
			?>
		</select>
		<div class="action-buttons">
			<button type="button"
				class="button-primary button"
				value="<?php esc_attr_e( 'Add new language', 'linguator' ); ?>"
				id="add-language"
				name="add-language"
			>
				<span class="dashicons dashicons-plus"></span><?php esc_html_e( 'Add new language', 'linguator' ); ?>
			</button>
		</div>
	</div>
</div>
<table id="languages" class="striped">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Language', 'linguator' ); ?></th>
			<th><?php esc_html_e( 'Remove', 'linguator' ); ?></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<table id="defined-languages" class="striped<?php echo empty( $existing_languages ) ? ' hide' : ''; ?>">
	<?php if ( ! empty( $default_language ) ) : ?>
		<caption><span class="icon-default-lang"></span> <?php esc_html_e( 'Default language', 'linguator' ); ?></caption>
	<?php endif; ?>
	<thead>
		<tr>
			<th><?php esc_html_e( 'Languages already defined', 'linguator' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ( $existing_languages as $lg ) {
		printf(
			'<tr><td>%3$s<span>%2$s - %1$s</span> %4$s</td></tr>' . "\n",
			esc_attr( $lg->locale ),
			esc_html( $lg->name ),
			$lg->flag, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$lg->is_default ? ' <span class="icon-default-lang"><span class="screen-reader-text">' . esc_html__( 'Default language', 'linguator' ) . '</span></span>' : ''
		);
	}
	?>
	</tbody>
</table>

<div id="dialog">
	<p>
	<?php
	printf(
		/* translators: %1$s: is a language flag image, %2$s: is a language native name */
		esc_html__( 'You selected %1$s %2$s but you didn\'t add it to the list before continuing to the next step.', 'linguator' ),
		'<span id="dialog-language-flag"></span>',
		'<strong id="dialog-language"></strong>'
	);
	?>
	</p>
	<p>
	<?php esc_html_e( 'Do you want to add this language before continuing to the next step?', 'linguator' ); ?>
	</p>
	<ul>
		<li>
			<?php
			printf(
				/* translators: %s: is the translated label of the 'Yes' button  */
				esc_html__( '%s: add this language and continue to the next step', 'linguator' ),
				'<strong>' . esc_html__( 'Yes', 'linguator' ) . '</strong >'
			);
			?>
		</li>
		<li>
		<?php
			printf(
				/* translators: %s: is the translated label of the 'No' button  */
				esc_html__( "%s: don't add this language and continue to the next step", 'linguator' ),
				'<strong>' . esc_html__( 'No', 'linguator' ) . '</strong >'
			);
			?>
		</li>
		<li>
		<?php
			printf(
				/* translators: %s: is the translated label of the 'Ignore' button  */
				esc_html__( '%s: stay at this step', 'linguator' ),
				'<strong>' . esc_html__( 'Ignore', 'linguator' ) . '</strong >'
			);
			?>
		</li>
	</ul>
</div>
