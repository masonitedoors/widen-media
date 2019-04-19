<?php
/**
 * Widen Media plugin for WordPress
 *
 * @package           Widen_Media
 *
 * @wordpress-plugin
 * Plugin Name:       Widen Media
 * Description:       Search and add Widen digital assets to your WordPress media library.
 * Version:           0.0.1
 * Author:            Masonite
 * Author URI:        https://www.masonite.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       widen-media
 * Domain Path:       /languages
 */

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

// Autoload the plugin classes.
require_once __DIR__ . '/inc/autoload.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in lib/class-activator.php
 */
\register_activation_hook( __FILE__, '\Masonite\Widen_Media\Activator::activate' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in lib/class-deactivator.php
 */
\register_deactivation_hook( __FILE__, '\Masonite\Widen_Media\Deactivator::deactivate' );

/**
 * Begins execution of the plugin.
 */
\add_action(
	'plugins_loaded',
	function () {
		$plugin = new \Masonite\Widen_Media\Plugin();
		$plugin->run();
	}
);
