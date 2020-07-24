<?php

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

/**
 * Utility methods.
 */
class Util {

	/**
	 * Remove the query string from a url.
	 *
	 * @param string $url The url to clean.
	 */
	public static function remove_query_string( $url ): string {
		return preg_replace( '/\?.*/', '', $url );
	}

	/**
	 * Sanitize our image URLs:
	 *
	 * Update .tif images to be .png.
	 * Remove query strings.
	 *
	 * @param string $image_url The image URL to sanitize.
	 */
	public static function sanitize_image_url( $image_url ): string {
		// Update .tif images to be .png.
		if ( strpos( $image_url, '.tif' ) !== false ) {
			$image_url = str_replace( '.tif', '.png', $image_url );
		}

		// Remove query string.
		$image_url = self::remove_query_string( $image_url );

		return $image_url;
	}

	/**
	 * Wrapper for error_log that handles both arrays and strings.
	 *
	 * @param string|array $log What we want logged.
	 * @return void
	 */
	public static function write_log( $log ): void {
		// phpcs:disable
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
		// phpcs:enable
	}

}
