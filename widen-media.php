<?php
/**
 * Widen Media plugin for WordPress
 *
 * @package           Widen_Media
 *
 * @wordpress-plugin
 * Plugin Name:       Widen Media
 * Description:       Search and add Widen digital assets to your WordPress media library.
 * Version:           0.0.2
 * Author:            Masonite
 * Author URI:        https://www.masonite.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       widen-media
 * Domain Path:       /languages
 */

declare( strict_types = 1 );

namespace Masonite\Widen_Media;

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

// Autoload the plugin classes.
require_once __DIR__ . '/inc/autoload.php';

/**
 * Requires running PHP 7.2 or above.
 */
function version_check() {
	if ( version_compare( PHP_VERSION, '7.2', '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( esc_html__( 'The Widen Media plugin requires PHP Version 7.2 or above.', 'widen-media' ) );
	}
}
add_action( 'admin_init', __NAMESPACE__ . '\version_check' );

/**
 * Begins execution of the plugin.
 */
function load_widen_media() {
	$plugin = new Plugin();
	$plugin->run();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\load_widen_media' );
