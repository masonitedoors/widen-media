<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @package    Widen_Media
 */

declare( strict_types = 1 );

namespace Masonite\Widen_Media;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    Widen_Media
 */
class Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @var      Widen_Media_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var      string    $pluginname    The string used to uniquely identify this plugin.
	 */
	protected $pluginname = 'widen-media';

	/**
	 * The current version of the plugin.
	 *
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version = '0.0.1';

	/**
	 * Defines the path to the plugin's root directory.
	 *
	 * @var      string    $plugin_dir_path    Defines the path to the plugin's root directory.
	 */
	protected $plugin_dir_path;

	/**
	 * Defines the url to the plugin's root directory.
	 *
	 * @var      string    $plugin_dir_url    Defines the url to the plugin's root directory.
	 */
	protected $plugin_dir_url;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 */
	public function __construct() {
		$this->loader = new Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 */
	private function set_locale() {
		$plugin_i18n = new I18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );
		$plugin_i18n->load_plugin_textdomain();
	}

	/**
	 * Define the path to the plugin's root directory.
	 */
	private function define_plugin_dir_path() {
		$this->plugin_dir_path = \plugin_dir_path( dirname( __FILE__ ) );
	}

	/**
	 * Define the url to the plugin's root directory.
	 */
	private function define_plugin_dir_url() {
		$this->plugin_dir_url = \plugin_dir_url( dirname( __FILE__ ) );
	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {
		$plugin_admin    = new Admin( $this );
		$plugin_basename = \plugin_basename( dirname( __FILE__, 2 ) ) . '/widen-media.php';

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_media_page' );
		$this->loader->add_action( 'wp_ajax_widen_media_form_submit', $plugin_admin, 'form_submit' );
		$this->loader->add_action( 'wp_ajax_widen_media_add_to_library', $plugin_admin, 'add_to_library' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'settings_link', 10, 4 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 */
	public function run() {
		$this->set_locale();
		$this->define_plugin_dir_path();
		$this->define_plugin_dir_url();
		$this->define_admin_hooks();
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->pluginname;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return Widen_Media_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return String The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the path to the plugin's root directory.
	 *
	 * @return String The path to the plugin's root directory.
	 */
	public function get_plugin_dir_path() {
		return $this->plugin_dir_path;
	}

	/**
	 * Retrieve the url to the plugin's root directory.
	 *
	 * @return String The url to the plugin's root directory.
	 */
	public function get_plugin_dir_url() {
		return $this->plugin_dir_url;
	}

}
