<?php

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @var Widen_Media_Loader
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var string
	 */
	protected $plugin_version;

	/**
	 * Defines the path to the plugin's root directory.
	 *
	 * @var string
	 */
	protected $plugin_dir_path;

	/**
	 * Defines the url to the plugin's root directory.
	 *
	 * @var string
	 */
	protected $plugin_dir_url;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 */
	public function __construct() {
		$this->plugin_name    = self::get_plugin_name();
		$this->plugin_version = self::get_plugin_version();
		$this->loader         = new Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 */
	private function set_locale() : void {
		$plugin_i18n = new I18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );
		$plugin_i18n->load_plugin_textdomain();
	}

	/**
	 * Define the path to the plugin's root directory.
	 */
	private function define_plugin_dir_path() : void {
		$this->plugin_dir_path = plugin_dir_path( dirname( __FILE__ ) );
	}

	/**
	 * Define the url to the plugin's root directory.
	 */
	private function define_plugin_dir_url() : void {
		$this->plugin_dir_url = plugin_dir_url( dirname( __FILE__ ) );
	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() : void {
		$plugin_admin    = new Admin();
		$plugin_basename = plugin_basename( dirname( __FILE__, 2 ) ) . '/widen-media.php';

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_media_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'settings_init' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'settings_link', 10, 4 );
		$this->loader->add_filter( 'wp_get_attachment_image_src', $plugin_admin, 'fix_widen_attachment_urls', 10, 4 );
		$this->loader->add_action( 'admin_post_handle_search_submit', $plugin_admin, 'handle_search_submit' );
		$this->loader->add_action( 'wp_ajax_widen_media_add_image_to_library', $plugin_admin, 'add_image_to_library' );
		$this->loader->add_action( 'wp_ajax_widen_media_add_audio_to_library', $plugin_admin, 'add_audio_to_library' );
		$this->loader->add_action( 'wp_ajax_widen_media_add_pdf_to_library', $plugin_admin, 'add_pdf_to_library' );

		// Prevent user from accessing the native 'add new' button for the WordPress Media Library.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'hide_add_new_media_menu' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'hide_core_media_buttons' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'edit_new_media_link', 90 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_frontend_hooks() : void {
		$plugin_frontend = new Frontend();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_frontend, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_frontend, 'enqueue_scripts' );
		$this->loader->add_shortcode( 'Widen_Media', $plugin_frontend, 'shortcode' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 */
	public function run() : void {
		$this->set_locale();
		$this->define_plugin_dir_path();
		$this->define_plugin_dir_url();
		$this->define_admin_hooks();
		$this->define_frontend_hooks();
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 */
	public function get_loader() : Widen_Media_Loader {
		return $this->loader;
	}

	/**
	 * Retrieve the path to the plugin's root directory.
	 */
	public function get_plugin_dir_path() : string {
		return $this->plugin_dir_path;
	}

	/**
	 * Retrieve the url to the plugin's root directory.
	 */
	public function get_plugin_dir_url() : string {
		return $this->plugin_dir_url;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public static function get_plugin_name() : string {
		$plugin_basename = plugin_basename( dirname( __DIR__ ) );

		return $plugin_basename;
	}

	/**
	 * Get the plugin's current version.
	 */
	public static function get_plugin_version() : string {
		$plugin_version = '';
		$path           = plugin_dir_path( dirname( __FILE__ ) ) . 'widen-media.php';
		$plugin_data    = get_file_data( $path, [ 'Version' => 'Version' ] );

		if ( ! empty( $plugin_data['Version'] ) ) {
			$plugin_version = $plugin_data['Version'];
		}

		return $plugin_version;
	}

}
