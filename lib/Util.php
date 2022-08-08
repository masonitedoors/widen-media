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
	 * Remove space characters from from a url.
	 *
	 * @param string $url The url to clean.
	 */
	public static function remove_spaces( $url ): string {
		return preg_replace( '/%20/', '_', $url );
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
		} elseif ( null === $log ) {
			error_log( 'null' );
		} else {
			error_log( (string) $log );
		}
		// phpcs:enable
	}

	/**
	 * Retrieves the attachment ID from the file URL.
	 *
	 * @param string $image_url The image URL.
	 *
	 * @link https://pippinsplugins.com/retrieve-attachment-id-from-image-url/
	 */
	public static function get_attachment_id( $image_url ): ?string {
		global $wpdb;

		$attachment = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE guid='%s';", // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.QuotedSimplePlaceholder
				esc_url( $image_url )
			)
		);

		if ( isset( $attachment[0] ) ) {
			$attachment_id = $attachment[0];

			return $attachment_id;
		}

		return null;
	}

	/**
	 * Check if attachment exists within the database.
	 *
	 * @param string $image_url The image URL.
	 */
	public static function attachment_exists( $image_url ): bool {
		$attachment_id = self::get_attachment_id( $image_url );

		if ( $attachment_id ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns placeholder image url saying "Preview not available".
	 */
	public static function placeholder_image_url() {
		$placeholder_img_url = plugin_dir_url( __DIR__ ) . 'dist/img/placeholder.jpg';
		return $placeholder_img_url;
	}

}
