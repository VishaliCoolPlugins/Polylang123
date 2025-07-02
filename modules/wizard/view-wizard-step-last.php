<?php
/**
 * Displays the wizard last step
 *
 * @package Linguator
 *
 * @since 2.7
 */

defined( 'ABSPATH' ) || exit;

?>
<h2><?php esc_html_e( "You're ready to translate your contents!", 'linguator' ); ?></h2>

<div class="documentation">
	<p><?php esc_html_e( "You're now able to translate your contents such as posts, pages, categories and tags. You can learn how to use Linguator by reading the documentation.", 'linguator' ); ?></p>
	<div class="documentation-container">
		<p class="lmat-wizard-actions step documentation-button-container">
			<a
				class="button button-primary button-large documentation-button"
				href="<?php echo esc_url( 'https://linguator.pro/doc-category/getting-started/' ); ?>"
				target="blank"
			>
				<?php esc_html_e( 'Read documentation', 'linguator' ); ?>
			</a>
		</p>
	</div>
</div>

<ul class="lmat-wizard-next-steps">
	<li class="lmat-wizard-next-step-item">
		<div class="lmat-wizard-next-step-description">
			<p class="next-step-heading"><?php esc_html_e( 'Next step', 'linguator' ); ?></p>
			<h3 class="next-step-description"><?php esc_html_e( 'Create menus', 'linguator' ); ?></h3>
			<p class="next-step-extra-info">
				<?php esc_html_e( 'To get your website ready, there are still two steps you need to perform manually: add menus in each language, and add a language switcher to allow your visitors to select their preferred language.', 'linguator' ); ?>
			</p>
		</div>
		<div class="lmat-wizard-next-step-action">
			<p class="lmat-wizard-actions step">
				<a class="button button-primary button-large" href="<?php echo esc_url( 'https://linguator.pro/doc/create-menus/' ); ?>">
					<?php esc_html_e( 'Read documentation', 'linguator' ); ?>
				</a>
			</p>
		</div>
	</li>
	<li class="lmat-wizard-next-step-item">
		<div class="lmat-wizard-next-step-description">
			<p class="next-step-heading"><?php esc_html_e( 'Next step', 'linguator' ); ?></p>
			<h3 class="next-step-description"><?php esc_html_e( 'Translate some pages', 'linguator' ); ?></h3>
			<p class="next-step-extra-info"><?php esc_html_e( "You're ready to translate the posts on your website.", 'linguator' ); ?></p>
		</div>
		<div class="lmat-wizard-next-step-action">
			<p class="lmat-wizard-actions step">
				<a class="button button-large" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">
					<?php esc_html_e( 'View pages', 'linguator' ); ?>
				</a>
			</p>
		</div>
	</li>
	<?php if ( ! defined( 'LINGUATOR_PRO' ) && ! defined( 'WOOCOMMERCE_VERSION' ) ) : ?>
		<li class="lmat-wizard-next-step-item">
			<div class="lmat-wizard-next-step-description">
				<p class="next-step-heading"><?php esc_html_e( 'Linguator Pro', 'linguator' ); ?></p>
				<h3 class="next-step-description"><?php esc_html_e( 'Upgrade to Linguator Pro', 'linguator' ); ?></h3>
				<p class="next-step-extra-info">
					<?php esc_html_e( 'Thank you for activating Linguator. If you want more advanced features - duplication, synchronization, REST API support, integration with other plugins, etc. - or further help provided by our Premium support, we recommend you upgrade to Linguator Pro.', 'linguator' ); ?>
				</p>
			</div>
			<div class="lmat-wizard-next-step-action">
				<p class="lmat-wizard-actions step">
					<a class="button button-primary button-large" href="<?php echo esc_url( 'https://linguator.pro/downloads/linguator-pro/' ); ?>">
						<?php esc_html_e( 'Buy now', 'linguator' ); ?>
					</a>
				</p>
			</div>
		</li>
	<?php endif; ?>
	<?php if ( ! defined( 'LINGUATOR_PRO' ) && defined( 'WOOCOMMERCE_VERSION' ) && ! defined( 'LMATWC_VERSION' ) ) : ?>
		<li class="lmat-wizard-next-step-item">
			<div class="lmat-wizard-next-step-description">
				<p class="next-step-heading"><?php esc_html_e( 'WooCommerce', 'linguator' ); ?></p>
				<h3 class="next-step-description"><?php esc_html_e( 'Purchase Linguator Business Pack', 'linguator' ); ?></h3>
				<p class="next-step-extra-info">
					<?php
					printf(
						/* translators: %s is the name of Linguator Business Pack product */
						esc_html__( 'We have noticed that you are using Linguator with WooCommerce. To ensure a better compatibility, we recommend you use %s which includes both Linguator Pro and Linguator For WooCommerce.', 'linguator' ),
						'<strong>' . esc_html__( 'Linguator Business Pack', 'linguator' ) . '</strong>'
					);
					?>
				</p>
			</div>
			<div class="lmat-wizard-next-step-action">
				<p class="lmat-wizard-actions step">
					<a class="button button-primary button-large" href="<?php echo esc_url( 'https://linguator.pro/downloads/linguator-for-woocommerce/' ); ?>">
						<?php esc_html_e( 'Buy now', 'linguator' ); ?>
					</a>
				</p>
			</div>
		</li>
	<?php endif; ?>
	<li class="lmat-wizard-additional-steps">
		<div class="lmat-wizard-next-step-action">
			<p class="lmat-wizard-actions step">
				<a class="button button-large" href="<?php echo esc_url( admin_url() ); ?>">
					<?php esc_html_e( 'Return to the Dashboard', 'linguator' ); ?>
				</a>
			</p>
		</div>
	</li>
</ul>
