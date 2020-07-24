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
	 * @param string $hook The page hook.
	 */
	private static function can_load_scripts( $hook ): bool {
		$screen = get_current_screen();

		if ( 'media_page_widen-media' === $hook || 'wm_collection' === $screen->post_type ) {
			return true;
		}

		return false;
	}

	/**
	 * Append our plugin's settings to the existing WordPress media settings.
	 */
	public function settings_init(): void {

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
	public function section_description(): void {
		esc_html_e( 'The options below are readonly and can only be set via wp-config.php.', 'widen-media' );
	}

	/**
	 * The callback for our access_token setting.
	 */
	public function access_token_setting_cb(): void {
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
	 * Register our image sizes with WordPress.
	 */
	public function register_image_sizes(): void {
		// From API.
		add_image_size( 'wm-thumbnail', 500, 500 );
		add_image_size( 'wm-pager', 64, 64 );

		// Manually added.
		add_image_size( 'wm-logo', 203, 49 );
		add_image_size( 'wm-icon', 103, 103 );
		add_image_size( 'wm-door-a', 125, 333 );
		add_image_size( 'wm-door-b', 216, 454 );
		add_image_size( 'wm-glass', 300, 300 );
		add_image_size( 'wm-slider', 240, 342 );
		add_image_size( 'wm-card', 640, 425 );
		add_image_size( 'wm-card-full-size', 816, 550 );
	}

	/**
	 * Make our custom image sizes selectable from WordPress admin.
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_image_size/#for-media-library-images-admin
	 *
	 * @param array $sizes The array of available image sizes.
	 */
	public function add_selectable_image_sizes( $sizes ): array {
		$widen_media_image_sizes = [
			'wm-thumbnail'      => __( 'Widen Media: Thumbnail', 'widen-media' ),
			'wm-pager'          => __( 'Widen Media: Pager', 'widen-media' ),
			'wm-logo'           => __( 'Widen Media: Logo', 'widen-media' ),
			'wm-icon'           => __( 'Widen Media: Icon', 'widen-media' ),
			'wm-door-a'         => __( 'Widen Media: Door A', 'widen-media' ),
			'wm-door-b'         => __( 'Widen Media: Door B', 'widen-media' ),
			'wm-glass'          => __( 'Widen Media: Glass', 'widen-media' ),
			'wm-slider'         => __( 'Widen Media: Slider', 'widen-media' ),
			'wm-card'           => __( 'Widen Media: Card', 'widen-media' ),
			'wm-card-full-size' => __( 'Widen Media: Full Size Card', 'widen-media' ),
		];

		return array_merge( $sizes, $widen_media_image_sizes );
	}

	/**
	 * Disable responsive images in WordPress.
	 *
	 * Mocking out behavior from disable-responsive-images plugin.
	 *
	 * @link https://github.com/josephfusco/disable-responsive-images
	 */
	public function disable_srcset(): int {
		return 1;
	}

	/**
	 * Handle WordPress core's `image_downsize` for the images added through this plugin.
	 *
	 * @param bool|array   $downsize Whether to short-circuit the image downsize.
	 * @param int          $id       Attachment ID for image.
	 * @param array|string $size     Requested size of image. Image size name, or array of width and height values (in that order).
	 */
	public function handle_image_downsize( $downsize, $id, $size ) {
		$widen_media_id = get_post_meta( $id, 'widen_media_id', true );

		// Check if this is an image from Widen.
		if ( ! empty( $widen_media_id ) ) {
			$meta = wp_get_attachment_metadata( $id );

			if ( isset( $meta['sizes'][ $size ] ) ) {
				// Use the individual widen media size.
				$image = $meta['sizes'][ $size ];

				$image_arr = [
					$image['file'],
					$image['width'],
					$image['height'],
					true,
				];
				return $image_arr;
			}
			return false;
		}
		return false;
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @param string $hook The page hook.
	 */
	public function enqueue_styles( $hook ): void {
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
	 * @param string $hook The page hook.
	 */
	public function enqueue_scripts( $hook ): void {
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
				'ajax_nonce' => wp_create_nonce( 'widen_media_ajax_request' ),
			]
		);
	}

	/**
	 * Register the options page for the plugin.
	 */
	public function register_media_page(): void {
		add_media_page(
			__( 'Widen Media Library', 'widen-media' ),
			__( 'Add New', 'widen-media' ),
			'manage_options',
			'widen-media',
			[ $this, 'assets_page_cb' ]
		);
	}

	/**
	 * Callback for the media assets page.
	 */
	public function assets_page_cb(): void {
		include_once 'Admin/widen-media.php';
	}

	/**
	 * Callback for the media assets page.
	 */
	public function collections_page_cb(): void {
		include_once 'Admin/widen-media-collections.php';
	}

	/**
	 * Add a link to the plugin's options page.
	 *
	 * @param array  $links       The plugin action links.
	 * @param string $plugin_file The plugin's main file.
	 * @param array  $plugin_data The plugin data.
	 * @param string $context     The context.
	 */
	public function settings_link( $links, $plugin_file, $plugin_data, $context ): array {
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
	protected static function get_settings_page_url(): string {
		return admin_url( 'options-media.php' );
	}

	/**
	 * Return the base url to the media assets page (search results).
	 */
	protected static function get_media_assets_page(): string {
		$base = admin_url( 'upload.php' );

		return add_query_arg( 'page', 'widen-media', $base );
	}

	/**
	 * Check if access token is defined.
	 */
	public static function is_access_token_defined(): bool {
		if ( ! defined( 'WIDEN_MEDIA_ACCESS_TOKEN' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Return the widen access token.
	 */
	public static function get_access_token(): ?string {
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
	 * @param array $item          A single item from the responses items array.
	 * @param bool  $is_collection If the current search is for a collection.
	 */
	public static function get_tile( $item, $is_collection = false ): void {
		$format_type = $item['file_properties']['format_type'] ?? 'unknown';

		include "Admin/tiles/$format_type.php";
	}

	/**
	 * Handles the form submission to search widen.
	 */
	public function handle_search_submit(): void {

		if ( ! empty( $_POST ) && check_admin_referer( 'search_submit', 'widen_media_nonce' ) ) {

			// Get the previous search query.
			if ( isset( $_POST['prev_search'] ) ) {
				$prev_query = rawurlencode( sanitize_text_field( wp_unslash( $_POST['prev_search'] ) ) );
			} else {
				$prev_query = null;
			}

			// Get the search query.
			if ( isset( $_POST['search'] ) ) {
				$query = rawurlencode( sanitize_text_field( wp_unslash( $_POST['search'] ) ) );
			} else {
				$query = '';
			}

			// Check is searching for a collection.
			if ( isset( $_POST['collection'] ) && '1' === $_POST['collection'] ) {
				$is_collection = true;
			} else {
				$is_collection = false;
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
			if ( $is_collection ) {
				$url = add_query_arg(
					[
						'search'     => $query,
						'paged'      => $paged,
						'collection' => '1',
					],
					$base_url
				);
			} else {
				$url = add_query_arg(
					[
						'search' => $query,
						'paged'  => $paged,
					],
					$base_url
				);
			}

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
		$widen_media_id = get_post_meta( $attachment_id, 'widen_media_id', true );

		// Check if this is an image from Widen.
		if ( ! empty( $widen_media_id ) ) {
			$image[0] = wp_get_attachment_url( $attachment_id );
		}

		return $image;
	}

	/**
	 * Filters the HTML content for the image tag.
	 *
	 * @since 2.6.0
	 *
	 * @param string       $html  HTML content for the image.
	 * @param int          $id    Attachment ID.
	 * @param string       $alt   Alternate text.
	 * @param string       $title Attachment title.
	 * @param string       $align Part of the class name for aligning the image.
	 * @param string|array $size  Size of image. Image size or array of width and height values (in that order).
	 *                            Default 'medium'.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/get_image_tag/
	 */
	public function filter_widen_image_tag( $html, $id, $alt, $title, $align, $size ): string {
		$widen_media_id = get_post_meta( $id, 'widen_media_id', true );

		// Do nothing special if this is not a Widen image.
		if ( empty( $widen_media_id ) ) {
			return $html;
		}

		list( $img_src, $width, $height ) = image_downsize( $id, $size );
		$hwstring                         = image_hwstring( $width, $height );

		$title = $title ? 'title="' . esc_attr( $title ) . '" ' : '';

		$class = 'align' . esc_attr( $align ) . ' size-' . esc_attr( $size ) . ' wp-image-' . $id;

		/**
		 * Filters the value of the attachment's image tag class attribute.
		 *
		 * @since 2.6.0
		 *
		 * @param string       $class CSS class name or space-separated list of classes.
		 * @param int          $id    Attachment ID.
		 * @param string       $align Part of the class name for aligning the image.
		 * @param string|array $size  Size of image. Image size or array of width and height values (in that order).
		 *                            Default 'medium'.
		 */
		$class = apply_filters( 'get_image_tag_class', $class, $id, $align, $size );

		// Get correct image src.
		$img_src = wp_get_attachment_url( $id );

		$html = '<img src="' . esc_attr( $img_src ) . '" alt="' . esc_attr( $alt ) . '" ' . $title . $hwstring . 'class="' . $class . '" />';

		return $html;
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
	public function add_image_to_library(): void {
		// Kill this process if this method wasn't called from our form.
		check_ajax_referer( 'widen_media_ajax_request', 'nonce' );

		// Set our default/fallback values.
		$asset_data = [
			'type'          => '',
			'id'            => '',
			'filename'      => '',
			'description'   => '',
			'mime_type'     => '',
			'url'           => '',
			'width'         => '',
			'height'        => '',
			'templated_url' => '',
			'fields'        => [],
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
		if ( isset( $_POST['templatedUrl'] ) ) {
			$asset_data['templated_url'] = sanitize_text_field( wp_unslash( $_POST['templatedUrl'] ) );
		}
		if ( isset( $_POST['fields'] ) ) {
			$asset_data['fields'] = sanitize_text_field( wp_unslash( $_POST['fields'] ) );
		}

		// Get asset size & mime type.
		if ( 'image' === $asset_data['type'] ) {
			// Original image sizes.
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
			'post_excerpt'   => '',                         // Attachment Caption.
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
			'file'   => $asset_data['url'],
			'sizes'  => [
				'wm-thumbnail'      => Widen::get_size_meta( $asset_data['templated_url'], 500, 500 ),
				'wm-pager'          => Widen::get_size_meta( $asset_data['templated_url'], 64, 64 ),
				'wm-logo'           => Widen::get_size_meta( $asset_data['templated_url'], 203, 49 ),
				'wm-icon'           => Widen::get_size_meta( $asset_data['templated_url'], 103, 103 ),
				'wm-door-a'         => Widen::get_size_meta( $asset_data['templated_url'], 125, 333 ),
				'wm-door-b'         => Widen::get_size_meta( $asset_data['templated_url'], 216, 454 ),
				'wm-glass'          => Widen::get_size_meta( $asset_data['templated_url'], 300, 300 ),
				'wm-slider'         => Widen::get_size_meta( $asset_data['templated_url'], 240, 342 ),
				'wm-card'           => Widen::get_size_meta( $asset_data['templated_url'], 640, 425 ),
				'wm-card-full-size' => Widen::get_size_meta( $asset_data['templated_url'], 816, 550 ),
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
		 * This is also used throughout this plugin in checking if an image is from Widen.
		 *
		 * @link https://developer.wordpress.org/reference/functions/update_post_meta/
		 */
		update_post_meta( $attachment_id, 'widen_media_id', $asset_data['id'] );

		/**
		 * Store custom Widen fields as post_meta.
		 */
		update_post_meta( $attachment_id, 'widen_media_fields', $asset_data['fields'] );

		// Exit since this is executed via Ajax.
		exit();
	}

	/**
	 * Save a collection to the wp_collection post type.
	 * This is called via ajax.
	 *
	 * @see src/scripts/admin.js
	 */
	public function save_collection(): void {
		// Kill this process if this method wasn't called from our form.
		check_ajax_referer( 'widen_media_ajax_request', 'nonce' );

		// Set our default/fallback values.
		$collection = [
			'title' => '',
			'items' => '',
		];

		if ( isset( $_POST['query'] ) ) {
			$collection['title'] = sanitize_text_field( wp_unslash( $_POST['query'] ) );
		}

		if ( isset( $_POST['items'] ) ) {
			$collection['items'] = sanitize_text_field( $_POST['items'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		}

		// Save the collection to our custom post type.
		$post_id = wp_insert_post(
			[
				'post_type'      => 'wm_collection',
				'post_title'     => $collection['title'],
				'post_content'   => '',
				'post_status'    => 'publish',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			]
		);

		/**
		 * Add our items to the collection.
		 *
		 * @link https://developer.wordpress.org/reference/functions/update_post_meta/
		 */
		update_post_meta( $post_id, 'items', $collection['items'] );

		// Exit since this is executed via Ajax.
		exit();
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
	 * Check if collection already exists within the database.
	 *
	 * @param string $collection_title The title of the collection. This should be the search query.
	 */
	public static function get_collection( $collection_title ): ?object {
		$collection = get_page_by_title( $collection_title, OBJECT, 'wm_collection' );

		if ( empty( $collection ) ) {
			return null;
		}

		return $collection;
	}

	/**
	 * Check if a collection already exists within the database.
	 *
	 * @param string $collection_title The title of the collection. This should be the search query.
	 */
	public static function collection_exists( $collection_title ): bool {
		$collection = self::get_collection( $collection_title );

		if ( $collection ) {
			return true;
		}

		return false;
	}

	/**
	 * Hide the add new media menu item.
	 */
	public function hide_add_new_media_menu(): void {
		remove_submenu_page( 'upload.php', 'media-new.php' );
	}

	/**
	 * Hide WordPress core media buttons.
	 */
	public function hide_core_media_buttons(): void {
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
	public function edit_new_media_link( $wp_admin_bar ): void {
		$new_content_node = $wp_admin_bar->get_node( 'new-media' );

		if ( $new_content_node ) {
			$new_content_node->href = '?page=widen-media';
			$wp_admin_bar->add_node( $new_content_node );
		}

	}

	/**
	 * Only allow new media to be added from Widen.
	 * This blocks files from being uploaded directly to the site.
	 *
	 * @param array $file The file array.
	 */
	public function disable_new_uploads( $file ): array {
		$file['error'] = __( 'Direct file uploads are not allowed. Please add media via Widen.', 'widen-media' );

		return $file;
	}

	/**
	 * Register the plugin's custom post types.
	 */
	public function register_post_types(): void {

		$labels = [
			'name'                  => _x( 'Collections', 'Post Type General Name', 'widen-media' ),
			'singular_name'         => _x( 'Collection', 'Post Type Singular Name', 'widen-media' ),
			'menu_name'             => __( 'Collections', 'widen-media' ),
			'name_admin_bar'        => __( 'Collection', 'widen-media' ),
			'archives'              => __( 'Collection Archives', 'widen-media' ),
			'attributes'            => __( 'Collection Attributes', 'widen-media' ),
			'parent_item_colon'     => __( 'Parent Collection:', 'widen-media' ),
			'all_items'             => __( 'Collections', 'widen-media' ),
			'add_new_item'          => __( 'Add New Collection', 'widen-media' ),
			'add_new'               => __( 'Add New', 'widen-media' ),
			'new_item'              => __( 'New Collection', 'widen-media' ),
			'edit_item'             => '', // Hide since we are making this `readonly`.
			'update_item'           => __( 'Update Collection', 'widen-media' ),
			'view_item'             => __( 'View Collection', 'widen-media' ),
			'view_items'            => __( 'View Collections', 'widen-media' ),
			'search_items'          => __( 'Search Collections', 'widen-media' ),
			'not_found'             => __( 'Not found', 'widen-media' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'widen-media' ),
			'featured_image'        => __( 'Featured Image', 'widen-media' ),
			'set_featured_image'    => __( 'Set featured image', 'widen-media' ),
			'remove_featured_image' => __( 'Remove featured image', 'widen-media' ),
			'use_featured_image'    => __( 'Use as featured image', 'widen-media' ),
			'insert_into_item'      => __( 'Insert into collection', 'widen-media' ),
			'uploaded_to_this_item' => __( 'Uploaded to this collection', 'widen-media' ),
			'items_list'            => __( 'Collection list', 'widen-media' ),
			'items_list_navigation' => __( 'Collection list navigation', 'widen-media' ),
			'filter_items_list'     => __( 'Filter collection list', 'widen-media' ),
		];
		$args   = [
			'label'               => __( 'Collection', 'widen-media' ),
			'description'         => __( 'Widen Media Collections', 'widen-media' ),
			'labels'              => $labels,
			'supports'            => [],
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'upload.php',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_in_rest'        => true,
			'capability_type'     => 'post',
			'capabilities'        => [
				'create_posts' => 'do_not_allow',
			],
			'map_meta_cap'        => true,
		];

		register_post_type( 'wm_collection', $args );

	}

	/**
	 * Remove quici edit from collections custom post type.
	 *
	 * @param array  $actions The row actions.
	 * @param object $post    The post object.
	 */
	public function remove_collections_quick_edit( $actions, $post ): array {
		// Only modify actions for our collections custom post type.
		if ( 'wm_collection' !== $post->post_type ) {
			return $actions;
		}

		unset( $actions['edit'] );
		unset( $actions['inline hide-if-no-js'] );

		return $actions;
	}

	/**
	 * Remove the default publish/update metabox from the wm_collection custom post type.
	 */
	public function remove_collections_submit_box(): void {
		remove_meta_box( 'submitdiv', 'wm_collection', 'side' );
	}

	/**
	 * Register the metaboxes we are using within the wm_collection post type.
	 */
	public function register_collection_meta_boxes(): void {

		add_meta_box(
			'submitdiv',
			__( 'Publish', 'widen-media' ),
			[ $this, 'collection_submitdiv_cb' ],
			'wm_collection',
			'side',
			'high'
		);

	}

	/**
	 * The callback for our custom submitdiv for the wp_collection post type.
	 */
	public function collection_submitdiv_cb(): void {
		include_once 'Admin/meta-boxes/collection-submit.php';
	}

	/**
	 * The callback fired when a collection is saved/updated.
	 *
	 * @param int $post_id The post ID for the collection being saved.
	 */
	public function save_post_collection_cb( $post_id ): void {
		$screen = get_current_screen();

		// Only hook into save_post for our wm_collection post type.
		if ( 'wm_collection' !== $screen->post_type ) {
			return;
		}

		$collection_wp_obj = get_post( $post_id );
		$query             = $collection_wp_obj->post_title;
		$response          = $this->widen->search_assets( $query, 0, 100, true );

		// Build JSON that we can use to store the collection.
		$json = self::json_image_query_data( $query, $response['items'] );
		$json = wp_slash( $json );

		/**
		 * Add our items to the collection.
		 *
		 * @link https://developer.wordpress.org/reference/functions/update_post_meta/
		 */
		update_post_meta( $post_id, 'items', $json );
	}

	/**
	 * The callback to display our custom markup for the wm_collection custom post type.
	 */
	public function view_collection_cb(): void {
		$screen = get_current_screen();

		// Do nothing if this is not our custom post type.
		if ( 'wm_collection' !== $screen->post_type ) {
			return;
		}

		// Grab the collection ID.
		$collection_id = get_the_ID();

		// Get the collection items.
		$items = json_decode( get_post_meta( $collection_id, 'items', true ) );

		$html  = '';
		$html .= '<ul class="collection-items">';

		foreach ( $items as $item ) {
			$fields = $item->fields;

			$html .= '<li class="collection-item">';
			$html .= '<div class="collection-item__thumbnail-wrapper">';
			$html .= '<img class="collection-item__thumbnail" src="' . esc_url( $item->thumbnail_url ) . '" alt="">';
			$html .= '</div>';
			$html .= '<table class="collection-item__fields-table">';

			foreach ( $fields as $key => $value ) {

				if ( is_array( $value ) ) {
					$value = $value[0] ?? '';
				}

				$html .= '<tr class="collection-item__field"><th scope="row">' . esc_html( $key ) . ': </th><td>' . esc_html( $value ) . '</td></tr>';
			}

			$html .= '</table>';
			$html .= '</li>';
		}

		$html .= '</ul>';

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}

	/**
	 * Returns JSON to be used within a page so we can grab it as needed via js.
	 * Used when adding image collections to the database.
	 *
	 * @param string $query The query title.
	 * @param array  $items The response items.
	 */
	public static function json_image_query_data( $query, $items ): string {
		$assets = [];

		foreach ( $items as $item ) {
			$id            = $item['id'] ?? '';
			$original_url  = $item['embeds']['original']['url'] ?? '';
			$thumbnail_url = $item['embeds']['ThumbnailPNG']['url'] ?? '';
			$pager_url     = $item['embeds']['PagerPNG']['url'] ?? '';
			$fields        = $item['metadata']['fields'] ?? [];

			// Change possible TIF url to PNG url.
			if ( strpos( $original_url, '.tif' ) !== false ) {
				$original_url = $item['embeds']['OriginalPNG']['url'];
			}

			// Remove query strings from urls.
			$original_url  = Util::remove_query_string( $original_url );
			$thumbnail_url = Util::remove_query_string( $thumbnail_url );
			$pager_url     = Util::remove_query_string( $pager_url );

			$assets[] = [
				'id'            => $id,
				'url'           => $original_url,
				'thumbnail_url' => $thumbnail_url,
				'pager_url'     => $pager_url,
				'fields'        => $fields,
			];
		}

		$json = wp_json_encode( $assets );

		return $json;
	}

}
