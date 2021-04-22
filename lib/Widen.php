<?php

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

/**
 * The Widen API-specific functionality of the plugin.
 *
 * @package Widen_Media
 */
class Widen {

	/**
	 * The base URL for Widen images.
	 *
	 * @var string
	 */
	public static $base_url = 'https://embed.widencdn.net/';

	/**
	 * The access token for Widen.
	 *
	 * @var string
	 */
	private $access_token;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $access_token The access token for Widen.
	 */
	public function __construct( $access_token ) {
		$this->access_token = $access_token;
	}

	/**
	 * Get the get request args.
	 *
	 * @param Integer $timeout The request timeout.
	 */
	public function get_request_args( $timeout = 10 ): array {
		$args = [
			'timeout' => $timeout,
			'headers' => [
				'Authorization' => "Bearer $this->access_token",
			],
		];

		return $args;
	}

	/**
	 * Search Widen assets.
	 *
	 * @param string  $query         The search query.
	 * @param int     $offset        The offset for the search.
	 * @param int     $limit         The response item limit.
	 * @param boolean $is_collection Search for a collection.
	 *
	 * @link https://widenv2.docs.apiary.io/#reference/assets/assets/list-by-search-query
	 */
	public function search_assets( $query, $offset = 0, $limit = 10, $is_collection = false ) {
		$base_url = 'https://api.widencollective.com/v2/assets/search';
		$before   = $is_collection ? 'acn:' : '';

		$url = add_query_arg(
			[
				'query'  => $before . $query,
				'offset' => $offset,
				'limit'  => $limit,
				'expand' => 'file_properties,metadata,embeds',
			],
			$base_url
		);

		// Make our request to Widen.
		$response = wp_remote_get( esc_url_raw( $url ), $this->get_request_args() );

		// Check the response code.
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 !== $response_code && ! empty( $response_message ) ) {
			return new \WP_Error( $response_code, $response_message );

		} elseif ( 200 !== $response_code ) {
			return new \WP_Error( $response_code, __( 'An unknown error occurred', 'widen-media' ) );

		} else {

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			return $data;
		}
	}

	/**
	 * Get an individual asset from Widen.
	 *
	 * @param string $id The asset ID.
	 *
	 * @link https://widenv2.docs.apiary.io/#reference/assets/assets/retrieve-by-id
	 */
	public function get_asset( $id ) {
		$base_url = "https://api.widencollective.com/v2/assets/$id";

		$url = add_query_arg(
			[
				'expand' => 'file_properties,metadata,embeds',
			],
			$base_url
		);

		// Make our request to Widen.
		$response = wp_remote_get( esc_url_raw( $url ), $this->get_request_args() );

		// Check the response code.
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 !== $response_code && ! empty( $response_message ) ) {
			return new \WP_Error( $response_code, $response_message );

		} elseif ( 200 !== $response_code ) {
			return new \WP_Error( $response_code, __( 'An unknown error occurred', 'widen-media' ) );

		} else {

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			return $data;
		}
	}

	/**
	 * List all collections from Widen.
	 *
	 * @param string $type The collection type.
	 *
	 * @link https://widenv2.docs.apiary.io/#reference/collections/collections
	 */
	public function list_collections( $type = 'global' ) {
		$base_url = 'https://api.widencollective.com/v2/collections/';

		$url = add_query_arg(
			[
				'type'   => $type,
				'offset' => 0,
			],
			$base_url
		);

		// Make our request to Widen.
		$response = wp_remote_get( esc_url_raw( $url ), $this->get_request_args() );

		// Check the response code.
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 !== $response_code && ! empty( $response_message ) ) {
			return new \WP_Error( $response_code, $response_message );

		} elseif ( 200 !== $response_code ) {
			return new \WP_Error( $response_code, __( 'An unknown error occurred', 'widen-media' ) );

		} else {

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			return $data;
		}
	}

	/**
	 * Returns a specific image size using widen's templated url.
	 *
	 * @param string $templated_url The templated url provided by Widen. This is returned in the image response.
	 * @param int    $width         The target image width.
	 * @param int    $height        The target image height.
	 * @param int    $scale         The scale of the image.
	 */
	public static function create_url_from_template( $templated_url, $width = null, $height = null, $scale = 1 ): string {
		$url = Util::sanitize_image_url( $templated_url );

		if ( $width && $height ) {
			$size = $width . 'x' . $height;
		} elseif ( $width ) {
			$size = $width;
		} else {
			$size = 'exact';
		}

		// Perform template string replacements.
		$url = str_replace( '{size}', $size, $url );
		$url = str_replace( '{scale}', $scale, $url );

		return $url;
	}

	/**
	 * Returns an associate array of image meta for a specific image size.
	 *
	 * @param string $templated_url The templated url provided by Widen. This is returned in the image response.
	 * @param int    $width         The target image width.
	 * @param int    $height        The target image height.
	 */
	public static function get_size_meta( $templated_url, $width = null, $height = null ): array {
		$file = self::create_url_from_template( $templated_url, $width, $height );

		$image_size = @getimagesize( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		$size_meta = [
			'file'      => $file,
			'width'     => $width,
			'height'    => $height,
			'mime-type' => $image_size['mime'],
		];

		return $size_meta;
	}
}
