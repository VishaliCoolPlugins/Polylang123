<?php
/**
 * Displays the content of the About metabox
 *
 * @package Linguator
 */

defined( 'ABSPATH' ) || exit;

?>
<p>
	<?php
	printf(
		/* translators: %1$s is link start tag, %2$s is link end tag. */
		esc_html__( 'Linguator is provided with an extensive %1$sdocumentation%2$s (in English). It includes information on how to set up your multilingual site and use it on a daily basis; FAQs, and documentation for developers to adapt their plugins and themes.', 'linguator' ),
		'<a href="https://linguator.pro/doc/">',
		'</a>'
	);
	if ( ! defined( 'LINGUATOR_PRO' ) ) {
		echo ' ';
		printf(
			/* translators: %1$s is link start tag, %2$s is link end tag. */
			esc_html__( 'Support and extra features are available to %1$sLinguator Pro%2$s users.', 'linguator' ),
			'<a href="https://linguator.pro">',
			'</a>'
		);
	}
	?>
</p>
