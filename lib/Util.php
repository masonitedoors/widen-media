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
	 * Returns a specific image size using widen's templated url.
	 *
	 * @param string $templated_url The templated url provided by Widen. This is returned in the image response.
	 * @param int    $width         The target image width.
	 * @param int    $height        The target image height.
	 * @param bool   $crop          If the image should be cropped.
	 * @param int    $scale         The scale of the image.
	 */
	public static function create_url_from_template( $templated_url, $width, $height = null, $crop = false, $scale = 1 ): string {
		$query_params = [
			'crop' => $crop ? 'true' : 'false',
		];

		$url = add_query_arg( $query_params, $templated_url );

		if ( $height ) {
			$size = $width . 'x' . $height;
		} else {
			$size = $width;
		}

		if ( $crop ) {
			$url = $url . "?crop=$crop";
		}

		$url = str_replace( '{size}', $size, $url );
		$url = str_replace( '{scale}', $scale, $url );

		return $url;
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

}
