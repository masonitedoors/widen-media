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
	 * @param String $label The button label.
	 * @param Array  $var   The var to display.
	 */
	public static function print( $label, $var ) : void {
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
	 * @param String $needle   The substring.
	 * @param String $haystack The string to search.
	 */
	public static function contains( $needle, $haystack ) : bool {
		return strpos( $haystack, $needle ) !== false;
	}

	/**
	 * Remove the query string from a url.
	 *
	 * @param String $url The url to clean.
	 */
	public static function remove_query_string( $url ) : string {
		return preg_replace( '/\?.*/', '', $url );
	}

	/**
	 * Sanitize a string removing any leading or trailing slashes..
	 *
	 * @param String $str The string to sanitize.
	 */
	public static function unslash_leading_trailing( $str ) : string {
		$str = untrailingslashit( $str );
		$str = ltrim( $str, '/' );

		return $str;
	}

	/**
	 * Encode data.
	 *
	 * @param Array $data The data to be encoded.
	 */
	public static function encode_data( $data ) : string {
		$encoded_data = base64_encode( wp_json_encode( $data ) ) ?? ''; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		return $encoded_data;
	}

	/**
	 * Decode data.
	 *
	 * @param String $data The data to be decoded.
	 */
	public static function decode_data( $data ) : ?object {
		$decoded_data = json_decode( base64_decode( $data ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		return $decoded_data;
	}

}
