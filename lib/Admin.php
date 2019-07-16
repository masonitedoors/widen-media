<?php

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the Widen Media, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 */
class Admin extends Plugin {

	/**
	 * The plugin's API for Widen.
	 *
	 * @var Widen
	 */
	private $widen;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		// Exit if not in wp-admin.
		if ( ! is_admin() ) {
			return;
		}

		$this->widen = new Widen( self::get_access_token() );
	}

	/**
	 * Check to see if we are able to load our plugin's admin scripts & styles.
	 *
	 * @param String $hook The page hook.
	 */
	private static function can_load_scripts( $hook ) : bool {
		switch ( $hook ) {
			case 'settings_page_widen-media':
			case 'media_page_widen-media-assets':
				return true;
			default:
				return false;
		}
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @param String $hook The page hook.
	 */
	public function enqueue_styles( $hook ) : void {
		if ( ! self::can_load_scripts( $hook ) ) {
			return;
		}

		wp_enqueue_style(
			self::get_plugin_name(),
			plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/admin.css',
			[],
			self::get_plugin_version(),
			'all'
		);
	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @param String $hook The page hook.
	 */
	public function enqueue_scripts( $hook ) : void {
		if ( ! self::can_load_scripts( $hook ) ) {
			return;
		}

		wp_enqueue_script(
			'fancybox',
			'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js',
			[ 'jquery' ],
			'3.5.7',
			true
		);

		wp_enqueue_script(
			'lazysizes',
			'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/4.1.5/lazysizes.min.js',
			[],
			'4.1.5',
			true
		);

		wp_enqueue_script(
			'lazysizes-blur-up',
			'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/4.1.5/plugins/blur-up/ls.blur-up.min.js',
			[],
			'4.1.5',
			true
		);

		wp_enqueue_script(
			self::get_plugin_name(),
			plugin_dir_url( dirname( __FILE__ ) ) . 'dist/scripts/admin.js',
			[],
			self::get_plugin_version(),
			'all'
		);

		wp_localize_script(
			self::get_plugin_name(),
			'widen_media',
			[
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'widen_media_add_to_library_nonce' ),
			]
		);
	}

	/**
	 * Register the options page for the plugin.
	 */
	public function register_media_page() : void {
		add_media_page(
			__( 'Widen Media Library', 'widen-media' ),
			__( 'Widen Media', 'widen-media' ),
			'manage_options',
			'widen-media-assets',
			[ $this, 'media_assets_page_cb' ]
		);

		add_submenu_page(
			'options-general.php',
			__( 'Widen Media Settings', 'widen-media' ),
			__( 'Widen Media', 'widen-media' ),
			'manage_options',
			'widen-media',
			[ $this, 'options_page_cb' ]
		);

		remove_submenu_page( 'upload.php', 'widen-media-asset' );
	}

	/**
	 * Callback for the media assets page.
	 */
	public function media_assets_page_cb() : void {
		include_once 'Admin/pages/media-assets.php';
	}

	/**
	 * Callback for the options page.
	 */
	public function options_page_cb() : void {
		include_once 'Admin/pages/options.php';
	}

	/**
	 * Add a link to the plugin's options page.
	 *
	 * @param array  $links       The plugin action links.
	 * @param string $plugin_file The plugin's main file.
	 * @param array  $plugin_data The plugin data.
	 * @param string $context     The context.
	 */
	public function settings_link( $links, $plugin_file, $plugin_data, $context ) : array {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $links;
		}

		// Add new item to the links array.
		array_unshift(
			$links,
			sprintf( '<a href="%s">%s</a>', esc_attr( self::get_settings_page_url() ), __( 'Settings', 'widen-media' ) )
		);

		return $links;
	}

	/**
	 * Return the plugin's settings page URL.
	 */
	protected static function get_settings_page_url() : string {
		$base = admin_url( 'options-general.php' );

		return add_query_arg( 'page', 'widen-media', $base );
	}

	/**
	 * Return the base url to the media assets page (search results).
	 */
	protected static function get_media_assets_page() : string {
		$base = admin_url( 'upload.php' );

		return add_query_arg( 'page', 'widen-media-assets', $base );
	}

	/**
	 * Check if access token is defined.
	 */
	public static function is_access_token_defined() : bool {
		if ( ! defined( 'WIDEN_MEDIA_ACCESS_TOKEN' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Return the widen access token.
	 */
	public static function get_access_token() : ?string {
		if ( self::is_access_token_defined() ) {
			return WIDEN_MEDIA_ACCESS_TOKEN;
		}
		return null;
	}

	/**
	 * Provides an easy eay to display an administration notice based on the incoming
	 * class and message.
	 *
	 * @param string $class   the class to add to the notice (warning, error, success).
	 * @param string $message the message to display in the administration notice area.
	 */
	public static function display_notice( $class, $message ) {
		printf(
			'<div class="notice notice-%1$s"><p>%2$s</p></div>',
			esc_attr( $class ),
			esc_html( $message )
		);
	}

	/**
	 * Get the appropriate tile template based on the item's format type (image, pdf, etc).
	 *
	 * @param Array $item A single item from the responses items array.
	 */
	public static function get_tile( $item ) : void {
		$format_type = $item['file_properties']['format_type'] ?? 'unknown';

		include "Admin/tiles/$format_type.php";
	}

	/**
	 * Handles the form submission to search widen.
	 */
	public function handle_search_submit() : void {

		if ( ! empty( $_POST ) && check_admin_referer( 'search_submit', 'widen_media_nonce' ) ) {

			// Get the previous search query.
			if ( isset( $_POST['prev_s'] ) ) {
				$prev_query = rawurlencode( sanitize_text_field( wp_unslash( $_POST['prev_s'] ) ) );
			} else {
				$prev_query = null;
			}

			// Get the search query.
			if ( isset( $_POST['s'] ) ) {
				$query = rawurlencode( sanitize_text_field( wp_unslash( $_POST['s'] ) ) );
			} else {
				$query = '';
			}

			// Get the pagination.
			// Reset the pagination if new search.
			if ( isset( $_POST['paged'] ) && $prev_query === $query ) {
				$paged = sanitize_text_field( wp_unslash( $_POST['paged'] ) );
			} else {
				$paged = 1;
			}

			$base_url = self::get_media_assets_page();

			// Build our search url.
			$url = add_query_arg(
				[
					's'     => $query,
					'paged' => $paged,
				],
				$base_url
			);

			wp_safe_redirect( $url );

			exit;

		}

	}

	/**
	 * Add widen asset to WordPress media library.
	 * This is called via ajax.
	 *
	 * @see src/scripts/admin.js
	 *
	 * $_POST['id']
	 * $_POST['filename]
	 * $_POST['description']
	 * $_POST['url']
	 */
	public function add_image_to_library() : void {
		// Kill this process if this method wasn't called from our form.
		check_ajax_referer( 'widen_media_add_to_library_nonce', 'nonce' );

		// Set our default/fallback values.
		$asset_data = [
			'type'        => '',
			'id'          => '',
			'filename'    => '',
			'description' => '',
			'url'         => '',
			'width'       => '',
			'height'      => '',
			'mime_type'   => '',
		];

		if ( isset( $_POST['type'] ) ) {
			$asset_data['type'] = sanitize_text_field( wp_unslash( $_POST['type'] ) );
		}
		if ( isset( $_POST['id'] ) ) {
			$asset_data['id'] = sanitize_text_field( wp_unslash( $_POST['id'] ) );
		}
		if ( isset( $_POST['filename'] ) ) {
			$asset_data['filename'] = sanitize_text_field( wp_unslash( $_POST['filename'] ) );
		}
		if ( isset( $_POST['description'] ) ) {
			$asset_data['description'] = sanitize_text_field( wp_unslash( $_POST['description'] ) );
		}
		if ( isset( $_POST['url'] ) ) {
			$asset_data['url'] = sanitize_text_field( wp_unslash( $_POST['url'] ) );
		}

		// Get asset size & mime type.
		if ( 'image' === $asset_data['type'] ) {
			$image_size              = getimagesize( $asset_data['url'] );
			$asset_data['width']     = $image_size[0];
			$asset_data['height']    = $image_size[1];
			$asset_data['mime_type'] = $image_size['mime'];
		}

		/**
		 * Prepare attachment & insert into WordPress Media Library.
		 *
		 * @link https://developer.wordpress.org/reference/functions/wp_insert_attachment/
		 */
		$attachment    = [
			'guid'           => $asset_data['url'],
			'post_mime_type' => $asset_data['mime_type'],
			'post_title'     => sanitize_title( $asset_data['filename'] ),
			'post_content'   => $asset_data['description'],
		];
		$attachment_id = wp_insert_attachment( $attachment );

		/**
		 * Add the metadata for our recently inserted attachment.
		 * This can only happen after the attachment exists within WordPress.
		 *
		 * @link https://developer.wordpress.org/reference/functions/wp_update_attachment_metadata/
		 */
		$attachment_metadata['sizes'] = [
			'full' => [
				'width'                    => $asset_data['width'],
				'height'                   => $asset_data['height'],
				'file'                     => $asset_data['url'],
				'_wp_attachment_image_alt' => $asset_data['description'],
			],
		];
		wp_update_attachment_metadata( $attachment_id, $attachment_metadata );

		// Exit since this is executed via Ajax.
		exit();
	}

}
