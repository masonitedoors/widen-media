<?php
/**
 * Plugin Name:       Widen Media
 * Description:       Search and add Widen digital assets to your WordPress media library.
 * Version:           2.5.2
 * Author:            Masonite
 * Author URI:        https://www.masonite.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       widen-media
 * Domain Path:       /languages
 */

namespace Masonite\WP\Widen_Media;

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

/**
 * Autoload the plugin's classes.
 */
require_once __DIR__ . '/inc/autoload.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in lib/Activator.php
 */
register_activation_hook( __FILE__, __NAMESPACE__ . '\Activator::activate' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in lib/Deactivator.php
 */
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\Deactivator::deactivate' );

/**
 * Begins execution of the plugin.
 */
add_action(
	'plugins_loaded',
	function () {
		$plugin = new Plugin();
		$plugin->run();

		/**
		 * Provide functions for other plugins/themes to use
		 * in order to interact with the data from this plugin.
		 */
		require_once __DIR__ . '/inc/api.php';
	}
);

// phpcs:disable
// 🚨 START REMOVE SECTION BEFORE COMMITTING 🚨
if ( ! function_exists( 'write_log' ) ) {
	function write_log( $log ) {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
}

/**
 * Prepares an attachment post object for JS, where it is expected to be JSON-encoded and fit into an Attachment model.
 *
 * @param int|WP_Post $attachment Attachment ID or object.
 */
function prepare_attachment_for_js( $attachment ) {
	// write_log( $attachment );
	return $attachment;
}
add_filter( 'wp_prepare_attachment_for_js', __NAMESPACE__ . '\prepare_attachment_for_js', 10, 1 );
// 🚨 END REMOVE SECTION BEFORE COMMITTING 🚨
// phpcs:enable
