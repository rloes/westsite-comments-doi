<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/rloes
 * @since      1.0.0
 *
 * @package    Ws_Comments_Doi
 * @subpackage Ws_Comments_Doi/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ws_Comments_Doi
 * @subpackage Ws_Comments_Doi/admin
 * @author     Robin | Westsite <loeseke@westsite-webdesign.de>
 */
class Ws_Comments_Doi_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ws_Comments_Doi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ws_Comments_Doi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$style_path = plugin_dir_path( __FILE__ ) . 'css/ws-comments-doi-admin.css';
		$style_url  = plugin_dir_url( __FILE__ ) . 'css/ws-comments-doi-admin.css';
		$version    = filemtime( $style_path );
		wp_register_style( "{$this->plugin_name}_admin", $style_url, array(), $version, 'all' );
		wp_register_style( 'notifyjs', plugin_dir_url( __FILE__ ) . 'css/snackbar.css', array(), "4", 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ws_Comments_Doi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ws_Comments_Doi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$script_path = plugin_dir_path( __FILE__ ) . 'js/ws-comments-doi-admin.js';
		$script_url  = plugin_dir_url( __FILE__ ) . 'js/ws-comments-doi-admin.js';
		$version     = filemtime( $script_path );
		wp_register_script( $this->plugin_name . "_admin", $script_url, array( 'jquery' ), $version, false );
		wp_localize_script( $this->plugin_name . "_admin", 'wsCommentsDoiConfig', array(
				'ajax_url'                       => admin_url( 'admin-ajax.php' ),
				"security"                       => wp_create_nonce( 'ws_comments_doi_nonce' ),
				"success_image"                  => plugin_dir_url( __FILE__ ) . "image/success.svg",
				"question_image"                 => plugin_dir_url( __FILE__ ) . "image/question.svg",
				"error_image"                    => plugin_dir_url( __FILE__ ) . "image/error.svg",
				"added_message"                  => __( "verified", "ws-comments-doi" ),
				"error_message"                  => __( "Error", "default" ),
				"all_deleted_confirmation"       => __( "Do you really want to delete all verified emails permanently?", "ws-comments-doi" ),
				"all_delete_confirmation_button" => __( "Delete", "default" ),
				"all_deleted_message"            => __( "All verified emails deleted", "ws-comments-doi" ),
				"deleted_message"                => __( "deleted", "ws-comments-doi" ),
				"undo_message"                   => __( "Undo", "ws-comments-doi" )
			)
		);
		//wp_register_script('notifyjs', plugin_dir_url(__FILE__) . 'js/notifyjs/snackbar.min.js', ['jquery'], '4', $in_footer=true);
	}

	public function add_settings() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ws-comments-doi-admin-settings.php';
		$admin_settings_class = new Ws_Comments_Doi_Admin_Settings( $this->plugin_name, $this->version );
		$admin_settings_class->add_custom_discussion_settings();
	}

	function handle_delete_verified_email() {
		if ( ! check_ajax_referer( 'ws_comments_doi_nonce', 'ws_comments_doi_nonce' )
		     || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ "message" => __( 'You are not allowed to add verified emails', 'ws-comments-doi' ) ] );
		}

// Remove email from verified emails option
		$email_to_delete = urldecode( $_POST['email'] );
		$verified_emails = get_option( 'verified_emails', array() );
		$verified_emails = array_filter( $verified_emails, function ( $email ) use ( $email_to_delete ) {
			return $email !== $email_to_delete;
		} );
		if ( update_option( 'verified_emails', array_values( $verified_emails ) ) ) { // reindex the array
			$this->_returnVerifiedEmailsAsJson($verified_emails);
		}
// Redirect back to the settings page
		wp_send_json_error();

	}

	function handle_delete_all_verified_emails() {

		if ( ! check_ajax_referer( 'ws_comments_doi_nonce', 'ws_comments_doi_nonce' )
		     || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ "message" => __( 'You are not allowed to add verified emails', 'ws-comments-doi' ) ] );
		}

		// Delete all verified emails
		if ( delete_option( 'verified_emails' ) ) {
			$verified_emails = [];
			$this->_returnVerifiedEmailsAsJson($verified_emails);
		}

		// Redirect back to the settings page
		wp_send_json_error();
	}

	function handle_ajax_add_verified_email() {
		if ( ! check_ajax_referer( 'ws_comments_doi_nonce', 'ws_comments_doi_nonce' ) ) {
			wp_die( "Hallo" );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
// Current user is not allowed
			wp_send_json_error( [ "message" => __( 'You are not allowed to add verified emails', 'ws-comments-doi' ) ] );
		}

// Validate and sanitize the email
		$new_email = sanitize_email( $_POST['email'] );
		if ( ! is_email( $new_email ) ) {
// Invalid email
			wp_send_json_error( [ "message" => __( 'Please enter a valid email address', 'ws-comments-doi' ) ] );
		}

// Add email to the verified emails list
		$verified_emails = get_option( 'verified_emails', array() );
		if ( ! in_array( $new_email, $verified_emails ) ) {
			$verified_emails[] = $new_email;
			if ( update_option( 'verified_emails', $verified_emails ) ) {
				$this->_returnVerifiedEmailsAsJson($verified_emails);
			}
		}
		wp_send_json_error();
	}

	private function _returnVerifiedEmailsAsJson($verified_emails){
		ob_start();
		include plugin_dir_path( __FILE__ ) . "partials/verified-email-list.php";
		$verified_emails_list = ob_get_clean();
		wp_send_json_success( [
			"verified_emails" => $verified_emails_list
		] );
	}

}
