<?php
/**
 * Load the plugin's classes
 *
 * @package Widen_Media
 */

declare( strict_types = 1 );

spl_autoload_register(
	function ( $class ) {
		$dirs   = [
			'lib/',
		];
		$prefix = 'Masonite\\Widen_Media';
		$len    = strlen( $prefix );
		foreach ( $dirs as $dir ) {
			$base_dir = \plugin_dir_path( dirname( __FILE__ ) ) . $dir;
			if ( strncmp( $prefix, $class, $len ) !== 0 ) {
				return;
			}
			$relative_class = substr( $class, $len );
			$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}
	}
);
