<?php
/**
 * Displays the translations fields for media
 * Needs WP 3.5+
 *
 * @package Linguator
 *
 * @var LMAT_Admin_Classic_Editor $this    LMAT_Admin_Classic_Editor object.
 * @var LMAT_Language             $lang    The media language. Default language if no language assigned yet.
 * @var int                      $post_ID The media Id.
 */

defined( 'ABSPATH' ) || exit;
?>
<p><strong><?php esc_html_e( 'Translations', 'linguator' ); ?></strong></p>
<table>
	<?php
	foreach ( $this->model->get_languages_list() as $language ) {
		if ( $language->term_id === $lang->term_id ) {
			continue;
		}
		?>
		<tr>
			<td class = "lmat-media-language-column"><span class = "lmat-translation-flag"><?php echo $language->flag; // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo esc_html( $language->name ); ?></td>
			<td class = "lmat-media-edit-column">
				<?php
				$translation_id = $this->model->post->get_translation( $post_ID, $language );
				if ( ! empty( $translation_id ) && $translation_id !== $post_ID ) {
					// The translation exists
					printf(
						'<input type="hidden" name="media_tr_lang[%s]" value="%d" />',
						esc_attr( $language->slug ),
						(int) $translation_id
					);
					echo $this->links->edit_post_translation_link( $translation_id ); // phpcs:ignore WordPress.Security.EscapeOutput
				} else {
					// No translation
					echo $this->links->new_post_translation_link( $post_ID, $language ); // phpcs:ignore WordPress.Security.EscapeOutput
				}
				?>
			</td>
		</tr>
		<?php
	} // End foreach
	?>
</table>
