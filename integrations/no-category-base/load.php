<?php
/**
 * Loads the integration with No Category Base.
 *
 * @package Linguator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

LMAT_Integrations::instance()->no_category_base = new LMAT_No_Category_Base();
LMAT_Integrations::instance()->no_category_base->init();
