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
			case 'media_page_widen-media-assets':
				return true;
			default:
				return false;
		}
	}

	/**
	 * Append our plugin's settings to the existing WordPress media settings.
	 */
	public function settings_init() : void {

		add_settings_section(
			'widen-media-general',
			'Widen Media',
			[ $this, 'section_description' ],
			'media'
		);

		add_settings_field(
			'widen-media-access-token',
			'Access Token',
			[ $this, 'access_token_setting_cb' ],
			'media',
			'widen-media-general'
		);

	}

	/**
	 * The description for our settings settings.
	 */
	public function section_description() : void {
		esc_html_e( 'The options below are readonly and can only be set via wp-config.php.', 'widen-media' );
	}

	/**
	 * The callback for our access_token setting.
	 */
	public function access_token_setting_cb() : void {
		?>
		<label for="access_token">
			<input disabled name="access_token" type="text" id="access_token" value="<?php echo esc_attr( $this->get_access_token() ); ?>" class="regular-text">
		</label>
		<?php if ( ! $this->is_access_token_defined() ) : ?>
			<p class="description"><?php esc_html_e( 'WIDEN_MEDIA_ACCESS_TOKEN must be defined within wp-config.php.', 'widen-media' ); ?></p>
		<?php endif; ?>
		<?php
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
			__( 'Add New', 'widen-media' ),
			'manage_options',
			'widen-media-assets',
			[ $this, 'media_assets_page_cb' ]
		);
	}

	/**
	 * Callback for the media assets page.
	 */
	public function media_assets_page_cb() : void {
		include_once 'Admin/widen-media-assets.php';
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
		return admin_url( 'options-media.php' );
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
	 * Filters the widen image src result.
	 * This is needed to fix the doubling of the URLs.
	 *
	 * @param array|false  $image         Either array with src, width & height, icon src, or false.
	 * @param int          $attachment_id Image attachment ID.
	 * @param string|array $size          Size of image. Image size or array of width and height values
	 *                                    (in that order). Default 'thumbnail'.
	 * @param bool         $icon          Whether the image should be treated as an icon. Default false.
	 */
	public function fix_widen_attachment_urls( $image, $attachment_id, $size, $icon ) {
		$widen_media_id = get_post_meta( $attachment_id, '_widen_media_id', true );

		// Check if this is an image from Widen.
		if ( ! empty( $widen_media_id ) ) {
			$image[0] = wp_get_attachment_url( $attachment_id );
		}

		return $image;
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
			$image_size              = @getimagesize( $asset_data['url'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
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
			'post_title'     => pathinfo( $asset_data['filename'], PATHINFO_FILENAME ),
			'post_content'   => $asset_data['description'], // Attachment Description.
			'post_excerpt'   => $asset_data['description'], // Attachment Caption.
		];
		$attachment_id = wp_insert_attachment( $attachment );

		/**
		 * Update the attachment's metadata.
		 * This can only happen after the attachment exists within WordPress.
		 *
		 * @link https://developer.wordpress.org/reference/functions/wp_update_attachment_metadata/
		 */
		$attachment_metadata = [
			'width'  => $asset_data['width'],
			'height' => $asset_data['height'],
			'sizes'  => [
				'full' => [
					'file'      => $asset_data['url'],
					'width'     => $asset_data['width'],
					'height'    => $asset_data['height'],
					'mime-type' => $asset_data['mime_type'],
				],
			],
		];
		wp_update_attachment_metadata( $attachment_id, $attachment_metadata );

		/**
		 * Update the attachment's Alternative Text.
		 *
		 * @link https://developer.wordpress.org/reference/functions/update_post_meta/
		 */
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $asset_data['description'] );

		/**
		 * Store the asset's ID from Widen as post_meta.
		 *
		 * @link https://developer.wordpress.org/reference/functions/update_post_meta/
		 */
		update_post_meta( $attachment_id, '_widen_media_id', $asset_data['id'] );

		/**
		 * Store the asset's ID from Widen as post_meta.
		 *
		 * @link https://developer.wordpress.org/reference/functions/update_post_meta/
		 */
		update_post_meta( $attachment_id, '_widen_media_id', $asset_data['id'] );

		// Exit since this is executed via Ajax.
		exit();
	}

	/**
	 * Retrieves the attachment ID from the file URL.
	 *
	 * @param String $image_url The image URL.
	 *
	 * @link https://pippinsplugins.com/retrieve-attachment-id-from-image-url/
	 */
	public static function get_attachment_id( $image_url ) : ?string {
		global $wpdb;

		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.QuotedSimplePlaceholder

		if ( isset( $attachment[0] ) ) {
			$attachment_id = $attachment[0];

			return $attachment_id;
		}

		return null;
	}

	/**
	 * Check if attachment exists within the database.
	 *
	 * @param String $image_url The image URL.
	 */
	public static function attachment_exists( $image_url ) : bool {
		$attachment_id = self::get_attachment_id( $image_url );

		if ( $attachment_id ) {
			return true;
		}

		return false;
	}

	/**
	 * Hide the add new media menu item.
	 */
	public function hide_add_new_media_menu() : void {
		remove_submenu_page( 'upload.php', 'media-new.php' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function hide_core_media_buttons() : void {
		$screen = get_current_screen();

		// Only inject custom styles for uploads page.
		if ( 'upload' !== $screen->id ) {
			return;
		}

		echo '<style>[href$="/media-new.php"],.button.edit-attachment{display:none;}</style>';
	}

	/**
	 * Modify the new media link within WP Admin Bar to point at our Widen Library page.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Toolbar instance.
	 */
	public function edit_new_media_link( $wp_admin_bar ) : void {
		$new_content_node = $wp_admin_bar->get_node( 'new-media' );

		$new_content_node->href = '?page=widen-media-assets';

		$wp_admin_bar->add_node( $new_content_node );
	}

	/**
	 * Only allow new media to be added from Widen.
	 * This blocks files from being uploaded directly to the site.
	 *
	 * @param Array $file The file array.
	 */
	public function disable_new_uploads( $file ) : array {
		$file['error'] = __( 'Direct file uploads are not allowed. Please add media via Widen.', 'widen-media' );

		return $file;
	}

}
