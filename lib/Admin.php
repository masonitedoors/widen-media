<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @package    Widen_Media
 */

declare( strict_types = 1 );

namespace Masonite\Widen_Media;

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Widen_Media
 */
class Admin {

	/**
	 * The plugin's instance.
	 *
	 * @var Plugin $plugin This plugin's instance.
	 */
	private $plugin;

	/**
	 * The media page hook.
	 * To be used when enqueueing scripts/styles.
	 *
	 * @var String $media_page_hook The Widen media admin page hook.
	 */
	private $media_page_hook = 'media_page_widen-media';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param Plugin $plugin This plugin's instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @param String $hook The page hook.
	 */
	public function enqueue_styles( $hook ) {
		if ( $this->media_page_hook !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'fancybox',
			'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css',
			[],
			'3.5.7',
			'all'
		);

		wp_enqueue_style(
			$this->plugin->get_plugin_name(),
			plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/admin.css',
			[],
			$this->plugin->get_version(),
			'all'
		);

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @param String $hook The page hook.
	 */
	public function enqueue_scripts( $hook ) {
		if ( $this->media_page_hook !== $hook ) {
			return;
		}

		wp_enqueue_script( 'lazysizes', 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/4.1.5/lazysizes.min.js', [], '4.1.5', true );
		wp_enqueue_script( 'lazysizes-blur-up', 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/4.1.5/plugins/blur-up/ls.blur-up.min.js', [], '4.1.5', true );

		wp_register_script(
			'jquery-3.4.0',
			'https://code.jquery.com/jquery-3.4.0.min.js',
			[],
			'3.4.0',
			true
		);

		wp_add_inline_script(
			'jquery-3.4.0',
			'var jQuery_3_4_0 = $.noConflict(true);'
		);

		wp_enqueue_script(
			'fancybox',
			'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js',
			[ 'jquery-3.4.0' ],
			'3.5.7',
			true
		);

		wp_enqueue_script(
			$this->plugin->get_plugin_name(),
			plugin_dir_url( dirname( __FILE__ ) ) . 'dist/scripts/admin.js',
			[ 'jquery-3.4.0', 'fancybox' ],
			$this->plugin->get_version(),
			true
		);

		wp_localize_script(
			$this->plugin->get_plugin_name(),
			'WIDEN_MEDIA_OBJ',
			[
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'widen_media_nonce' ),
			]
		);
	}

	/**
	 * Register the options page for the plugin.
	 */
	public function register_media_page() {
		add_media_page(
			__( 'Widen Media Library', 'widen-media' ),
			__( 'Widen Media', 'widen-media' ),
			'manage_options',
			'widen-media',
			[ $this, 'media_page_cb' ]
		);

		add_submenu_page(
			'options-general.php',
			__( 'Widen Media Settings', 'widen-media' ),
			__( 'Widen Media', 'widen-media' ),
			'manage_options',
			'widen-media',
			[ $this, 'options_page_cb' ]
		);
	}

	/**
	 * Callback for the media page.
	 */
	public function media_page_cb() {
		include_once 'Admin/media-page.php';
	}

	/**
	 * Callback for the options page.
	 */
	public function options_page_cb() {
		include_once 'Admin/options-page.php';
	}

	/**
	 * Add a link to the plugin's options page.
	 *
	 * @param array  $links       The plugin action links.
	 * @param string $plugin_file The plugin's main file.
	 * @param array  $plugin_data The plugin data.
	 * @param string $context     The context.
	 *
	 * @return array
	 */
	public function settings_link( $links, $plugin_file, $plugin_data, $context ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		array_unshift(
			$links,
			sprintf( '<a href="%s">%s</a>', esc_attr( self::get_settings_page_url() ), __( 'Settings', 'widen-media' ) )
		);

		return $links;
	}

	/**
	 * Return the plugin's settings page URL.
	 *
	 * @return string
	 */
	protected function get_settings_page_url() {
		$base = admin_url( 'options-general.php' );

		return add_query_arg( 'page', 'widen-media', $base );
	}

	/**
	 * Check if access token is defined.
	 *
	 * @return Boolean
	 */
	public static function is_access_token_defined() {
		if ( ! defined( 'WIDEN_MEDIA_ACCESS_TOKEN' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Return the widen access token.
	 *
	 * @return string|null
	 */
	public static function get_access_token() {
		if ( self::is_access_token_defined() ) {
			return WIDEN_MEDIA_ACCESS_TOKEN;
		}
		return null;
	}

	/**
	 * Process search form data.
	 *
	 * @uses $_POST['query'] directly
	 */
	public function form_submit() {
		check_ajax_referer( 'widen_media_nonce', 'nonce' );

		if ( isset( $_POST['query'] ) ) {
			$query = sanitize_text_field( wp_unslash( $_POST['query'] ) );
		}

		$widen = new Widen( self::get_access_token() );
		$widen->search( $query );

		$data['message'] = 'Success!';
		$data['query']   = $query;

		wp_send_json_success( $data );
	}

	/**
	 * Add widen asset to WordPress media library.
	 */
	public function add_to_library() {
		check_ajax_referer( 'widen_media_nonce', 'nonce' );

		if ( isset( $_POST['item'] ) ) {
			$item_str = sanitize_text_field( wp_unslash( $_POST['item'] ) );
			$item     = json_decode( $item_str );
		}

		$filename   = $item->filename;
		$url        = $item->imageUrl->exact; // phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$image_size = getimagesize( $url );
		$width      = $image_size[0];
		$height     = $image_size[1];
		$mime_type  = $image_size['mime'];

		if ( empty( $image_size ) ) {
			if ( empty( $mime_type ) ) {
				$response = wp_remote_head( $url );
				if ( is_array( $response ) && isset( $response['headers']['content-type'] ) ) {
					$input['mime-type'] = $response['headers']['content-type'];
				}
			}
			$input['error'] = __( 'Unable to get the image size.', 'widen-media' );
			return $input;
		}

		$attachment          = [
			'guid'           => $url,
			'post_mime_type' => $mime_type,
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
			'post_content'   => $item->description,
		];
		$attachment_metadata = [
			'width'  => $width,
			'height' => $height,
			'file'   => $url,
		];

		$attachment_metadata['sizes'] = [ 'full' => $attachment_metadata ];
		$attachment_id                = wp_insert_attachment( $attachment );

		wp_update_attachment_metadata( $attachment_id, $attachment_metadata );

		$json['message'] = __( 'Added to library!', 'widen-media' );

		wp_send_json_success( $json );
	}
}
