<?php
/**
 * Plugin Name:       Widen Media
 * Description:       Search and add Widen digital assets to your WordPress media library.
 * Version:           2.6.7
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
