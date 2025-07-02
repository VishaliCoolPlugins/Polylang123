<?php
/**
 * Loads the integration with Aqua Resizer.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

LMAT_Integrations::instance()->aq_resizer = new LMAT_Aqua_Resizer();
LMAT_Integrations::instance()->aq_resizer->init();
