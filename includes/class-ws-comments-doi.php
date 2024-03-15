<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/rloes
 * @since      1.0.0
 *
 * @package    Ws_Comments_Doi
 * @subpackage Ws_Comments_Doi/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ws_Comments_Doi
 * @subpackage Ws_Comments_Doi/includes
 * @author     Robin | Westsite <loeseke@westsite-webdesign.de>
 */
class Ws_Comments_Doi {
	//TODO: Add privacy checkbox ins settings and frontend
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ws_Comments_Doi_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WS_COMMENTS_DOI_VERSION' ) ) {
			$this->version = WS_COMMENTS_DOI_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ws-comments-doi';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ws_Comments_Doi_Loader. Orchestrates the hooks of the plugin.
	 * - Ws_Comments_Doi_i18n. Defines internationalization functionality.
	 * - Ws_Comments_Doi_Admin. Defines all hooks for the admin area.
	 * - Ws_Comments_Doi_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ws-comments-doi-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ws-comments-doi-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ws-comments-doi-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ws-comments-doi-public.php';

		if ( ! defined( "DOIHELPER_VERSION" ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/plugins/doi-helper/doi-helper.php';
		}

		$this->loader = new Ws_Comments_Doi_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ws_Comments_Doi_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ws_Comments_Doi_i18n();

		$this->loader->add_action( 'plugins_loaded', [ $plugin_i18n, 'load_plugin_textdomain' ] );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ws_Comments_Doi_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', [ $plugin_admin, 'enqueue_styles' ] );
		$this->loader->add_action( 'admin_enqueue_scripts', [ $plugin_admin, 'enqueue_scripts' ] );
		$this->loader->add_action( 'admin_init', [ $plugin_admin, "add_settings" ] );
		$this->loader->add_action( 'wp_ajax_ws_comments_doi_delete_verified_email', [
			$plugin_admin,
			"handle_delete_verified_email"
		] );
		$this->loader->add_action( 'wp_ajax_ws_comments_doi_delete_all_verified_emails', [
			$plugin_admin,
			"handle_delete_all_verified_emails"
		] );
		$this->loader->add_action( 'wp_ajax_ws_comments_doi_add_verified_email', [
			$plugin_admin,
			"handle_ajax_add_verified_email"
		] );
		$this->loader->add_filter( 'script_loader_tag', [ $this, 'load_as_ES6' ], 10, 3 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ws_Comments_Doi_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', [ $plugin_public, 'enqueue_styles' ] );
		$this->loader->add_action( 'wp_enqueue_scripts', [ $plugin_public, 'enqueue_scripts' ] );
		$this->loader->add_filter( 'plugins_loaded', [ $plugin_public, 'register_doi_agent' ] );
		$this->loader->add_action( 'template_redirect', [ $plugin_public, 'maybe_send_confirmation_email_again' ] );
		$this->loader->add_filter( 'pre_comment_approve', [ $plugin_public, 'pre_comment_approved' ], 999, 2 );
		$this->loader->add_action( 'comment_post', [ $plugin_public, 'comment_post' ], 999, 3 );
		$this->loader->add_filter( 'wp_list_comments_args', [
			$plugin_public,
			"maybe_add_custom_comment_moderation_message"
		] );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Ws_Comments_Doi_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

	public function load_as_ES6(
		$tag, $handle, $source
	) {
		if ( in_array($handle, ["{$this->plugin_name}_admin", "{$this->plugin_name}_public"])) {
			$tag = '<script src="' . $source . '" type="module" ></script>';
		}

		return $tag;
	}

}
