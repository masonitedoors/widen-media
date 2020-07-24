<?php

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

/**
 * Utility methods.
 */
class Util {

	/**
	 * Wrapper for print_r for quick debugging.
	 * Debugging should be done with Xdebug.
	 *
	 * @param string $label The button label.
	 * @param array  $var   The var to display.
	 */
	public static function print( $label, $var ): void {
		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
		?>
		<br class="clear" />
		<details>
			<summary class="button button-secondary button-large" style="user-select: none; opacity: 0.5;"><?php echo esc_html( $label ); ?></summary>
			<pre style="background-color: #333; color: #fff; padding: 10px; margin-right: 20px; border-radius: 3px;"><?php print_r( $var ); ?></pre>
		</details>
		<?php
		// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_print_r
	}

	/**
	 * Check if string for substring.
	 * Improves readability.
	 *
	 * @param string $needle   The substring.
	 * @param string $haystack The string to search.
	 */
	public static function contains( $needle, $haystack ): bool {
		return strpos( $haystack, $needle ) !== false;
	}

	/**
	 * Remove the query string from a url.
	 *
	 * @param string $url The url to clean.
	 */
	public static function remove_query_string( $url ): string {
		return preg_replace( '/\?.*/', '', $url );
	}

	/**
	 * Sanitize a string removing any leading or trailing slashes..
	 *
	 * @param string $str The string to sanitize.
	 */
	public static function unslash_leading_trailing( $str ): string {
		$str = untrailingslashit( $str );
		$str = ltrim( $str, '/' );

		return $str;
	}

	/**
	 * Encode data.
	 *
	 * @param array $data The data to be encoded.
	 */
	public static function encode_data( $data ): string {
		$encoded_data = base64_encode( wp_json_encode( $data ) ) ?? ''; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		return $encoded_data;
	}

	/**
	 * Decode data.
	 *
	 * @param string $data The data to be decoded.
	 */
	public static function decode_data( $data ): ?object {
		$decoded_data = json_decode( base64_decode( $data ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		return $decoded_data;
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
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}

}
