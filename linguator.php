<?php
/**
 * Linguator
 *
 * @package           Linguator
 * @author            WP SYNTEX
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Linguator
 * Plugin URI:        https://linguator.pro
 * Description:       Adds multilingual capability to WordPress
 * Version:           3.7.3
 * Requires at least: 6.2
 * Requires PHP:      7.2
 * Author:            WP SYNTEX
 * Author URI:        https://linguator.pro
 * Text Domain:       linguator
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * Copyright 2011-2019 Frédéric Demarle
 * Copyright 2019-2025 WP SYNTEX
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}

if ( defined( 'LINGUATOR_VERSION' ) ) {
	// The user is attempting to activate a second plugin instance, typically Linguator and Linguator Pro.
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-includes/pluggable.php';
	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate this plugin.
		// WP does not allow us to send a custom meaningful message, so just tell the plugin has been deactivated.
		wp_safe_redirect( add_query_arg( 'deactivate', 'true', remove_query_arg( 'activate' ) ) );
		exit;
	}
} else {
	// Go on loading the plugin
	define( 'LINGUATOR_VERSION', '3.7.3' );
	define( 'LMAT_MIN_WP_VERSION', '6.2' );
	define( 'LMAT_MIN_PHP_VERSION', '7.2' );

	define( 'LINGUATOR_FILE', __FILE__ );
	define( 'LINGUATOR_DIR', __DIR__ );

	// Whether we are using Linguator or Linguator Pro, get the filename of the plugin in use.
	if ( ! defined( 'LINGUATOR_ROOT_FILE' ) ) {
		define( 'LINGUATOR_ROOT_FILE', __FILE__ );
	}

	if ( ! defined( 'LINGUATOR_BASENAME' ) ) {
		define( 'LINGUATOR_BASENAME', plugin_basename( __FILE__ ) ); // Plugin name as known by WP.
		require __DIR__ . '/vendor/autoload.php';
	}

	define( 'LINGUATOR', ucwords( str_replace( '-', ' ', dirname( LINGUATOR_BASENAME ) ) ) );

	if ( empty( $_GET['deactivate-linguator'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		new Linguator();
	}
}
