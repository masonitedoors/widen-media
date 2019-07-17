<?php

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

/**
 * The public-facing functionality of the plugin.
 */
class Frontend extends Plugin {

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		// Exit if not frontend of site.
		if ( is_admin() ) {
			return;
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() : void {
		wp_enqueue_style(
			self::get_plugin_name(),
			plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/frontend.css',
			[],
			self::get_plugin_version(),
			'all'
		);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_scripts() : void {
		wp_enqueue_script(
			self::get_plugin_name(),
			plugin_dir_url( dirname( __FILE__ ) ) . 'dist/scripts/frontend.js',
			[],
			self::get_plugin_version(),
			'all'
		);
	}

}
