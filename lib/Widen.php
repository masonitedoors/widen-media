<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @package    Widen_Media
 */

declare( strict_types = 1 );

namespace Masonite\Widen_Media;

/**
 * The Widen API-specific functionality of the plugin.
 *
 * @package    Widen_Media
 */
class Widen {

	/**
	 * The access token for Widen.
	 *
	 * @var    String $access_token The access token for Widen.
	 */
	private $access_token;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param String $access_token The access token for Widen.
	 */
	public function __construct( $access_token ) {
		$this->access_token = $access_token;
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @param String $query The search query.
	 *
	 * @link https://widenv2.docs.apiary.io/
	 */
	public function search( $query ) {
		$base_url = 'https://api.widencollective.com/v2/assets/search';

		$url = add_query_arg(
			[
				'query'  => "$query",
				'limit'  => '100',
				'expand' => 'file_properties,metadata',
			],
			$base_url
		);

		$args = [
			'timeout' => 10,
			'headers' => [
				'Authorization' => "Bearer $this->access_token",
			],
		];

		$response = wp_remote_get( esc_url_raw( $url ), $args );

		// Check the response code.
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 !== $response_code && ! empty( $response_message ) ) {
			$error = new WP_Error( $response_code, $response_message );
			wp_send_json_error( $error );
		} elseif ( 200 !== $response_code ) {
			$error = new WP_Error( $response_code, 'An unknown error occurred', 'Some information' );
			wp_send_json_error( $error );
		} else {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			wp_send_json_success( $data, $response_code );
		}
	}

}
