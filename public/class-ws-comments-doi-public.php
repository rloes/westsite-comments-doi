<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/rloes
 * @since      1.0.0
 *
 * @package    Ws_Comments_Doi
 * @subpackage Ws_Comments_Doi/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ws_Comments_Doi
 * @subpackage Ws_Comments_Doi/public
 * @author     Robin | Westsite <loeseke@westsite-webdesign.de>
 */
class Ws_Comments_Doi_Public {

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
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_register_style( "{$this->plugin_name}_public", plugin_dir_url( __FILE__ ) . 'css/ws-comments-doi-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		$script_path = plugin_dir_path( __FILE__ ) . 'js/ws-comments-doi-public.js';
		$script_url  = plugin_dir_url( __FILE__ ) . 'js/ws-comments-doi-public.js';
		$version     = filemtime( $script_path );
		wp_register_script( $this->plugin_name . "_public", $script_url, array(), $version, $in_footer = true );
		wp_localize_script( "{$this->plugin_name}_public", "wsCommentsDoiConfig", array(
				"moderation_text"     => get_option( 'ws_comments_doi_unverified_note',
					__( "Your comment is not visible yet. Please verify your email through the link sent to you", "ws-comment-doi", "ws-comments-doi" ) ),
				"link_text"           => get_option( 'ws_comments_doi_send_again_link_text',
					__( "Send confirmation email again", "ws-comment-doi" ) ),
				"succes_message_text"  => get_option( 'ws_comments_doi_send_again_success_message',
					__( "Confirmation mail sent again", "ws-comment-doi" ) ),
				'email_sent'          => get_query_var( 'email_sent_successfully', false ),
				"success_image"       => plugins_url( "../admin/image/success.svg", __FILE__ ),
				"question_image"      => plugins_url( "../admin/image/question.svg", __FILE__ ),
				"error_image"         => plugins_url( "..admin/image/error.svg", __FILE__ ),
			)
		);
		wp_register_style( 'notifyjs', plugins_url( "../admin/css/snackbar.css", __FILE__ ), array(), "4", 'all' );
	}

	/**
	 *
	 * @wordpress pre_comment_approved
	 * @since    1.0.0
	 */
	public function pre_comment_approved( $approved, $commentdata ) {
		if ( in_array( $approved, [ 0, 1 ] ) && ! is_user_logged_in() ) {
			$email = sanitize_email( $commentdata['comment_author_email'] );
			// Only send verification if the email isn't already verified
			if ( ! $this->is_email_verified( $email ) ) {
				$approved = 0;
			}
		}

		return $approved;
	}

	/**
	 * If not approved
	 *
	 * @param $comment_id
	 * @param $comment_approved
	 * @param $commentdata
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function comment_post( $comment_id, $comment_approved, $commentdata ) {
		if ( 0 === $comment_approved ) {
			$email = $commentdata['comment_author_email'];
			//add_query_arg('comment-submitted', 'awaiting-moderation');
			$token = doihelper_start_session( 'ws_comments_doi', array(
				'email_to'   => $email,
				'properties' => array(
					'comment_id'   => $comment_id,
					'comment_name' => $commentdata['comment_author']
				)
			) );
			update_comment_meta( $comment_id, '_doi_token', $token );
		}
	}

	public function register_doi_agent() {
		doihelper_register_agent( 'ws_comments_doi', array(
			'acceptance_period' => 86400, // 24 hours
			'optin_callback'    => [ $this, 'opt_in_callback' ],
			'email_callback'    => [ $this, 'email_callback' ]
		) );
	}

	public function opt_in_callback( $properties ) {
		$comment_id = $properties['comment_id'];
		error_log( print_r( $comment_id, true ) );
		// Mark the email as verified
		$comment = get_comment( $comment_id );
		if ( $comment ) {
			$verified_emails = get_option( 'verified_emails', array() );
			if ( ! in_array( $comment->comment_author_email, $verified_emails ) ) {
				$verified_emails[] = $comment->comment_author_email;
				update_option( 'verified_emails', $verified_emails );
			}

			// Approve the comment
			wp_set_comment_status( $comment_id, 'approve' );
			delete_comment_meta( $comment_id, "_doi_token" );

			$comment_link = get_comment_link( $comment_id );
			wp_redirect( $comment_link );
			exit;
		}
	}

	public function is_email_verified( $email ) {
		// Assume we store verified emails in an option array
		$verified_emails = get_option( 'verified_emails', array() );

		return in_array( $email, $verified_emails );
	}

	function maybe_add_custom_comment_moderation_message( $args ) {
		// Check if our custom query arg is present
		if ( isset( $_GET['unapproved'] ) ) {
			// Add a filter to modify the comment response text
			wp_enqueue_script( "{$this->plugin_name}_public" );
		}

		return $args;
	}

	function maybe_send_confirmation_email_again() {
		// trigger send_again if var is set.
		if ( isset( $_GET['unapproved'] ) && isset( $_GET['send_again'] ) ) {
			$comment = get_comment( $_GET['unapproved'] );
			if ( $comment && $comment->comment_approved === "0" ) {
				$token = get_comment_meta( $comment->comment_ID, '_doi_token', true );
				if ( $token ) {
					if ( $this->email_callback( [
						"email_to"     => $comment->comment_author_email,
						"comment_name" => $comment->comment_author,
						"token"        => $token
					] ) ) {
						//wp_enqueue_style( "{$this->plugin_name}_public" );
						set_query_var( 'email_sent_successfully', true );
						wp_enqueue_style( "notifyjs" );
					}
				}
			} else { // if request is made but comment is approved, spam, trash or non-existent remove send-again from request
				$updated_url = remove_query_arg( array(
					'unapproved',
					'send_again',
					'moderation-hash'
				), $_SERVER['REQUEST_URI'] );
				wp_redirect( $updated_url );
				exit; // Always call exit after wp_redirect

				/*unset($_GET["unapproved"]);
				unset($_GET["send_again"]);*/
			}
		}
	}

	public function email_callback( $args ) {
		$site_title = wp_specialchars_decode(
			get_bloginfo( 'name' ),
			ENT_QUOTES
		);

		$link = add_query_arg(
			array( 'doitoken' => $args['token'] ),
			home_url()
		);

		$to = $args['email_to'];

		$subject = get_option( 'ws_comments_doi_subject_text',
			__( "{$site_title}: Verify your email before commenting", "ws-comments-doi" ) );

		$message = get_option( 'ws_comments_doi_email_content' );
		if ( ! $message ) {
			$default_email = <<<EOT
Hello {{name}},

Thank you for your recent comment on our website.

To ensure the authenticity and security of submissions, we require a quick email verification. Your comment will be posted on our site once your email address is confirmed.

Please click the link below to verify your email and complete the process:

{{link}}
If you did not submit a comment or are unaware of this email, please disregard this message and do not click the link.

Thank you for your participation and understanding.

Best regards!
EOT;
			$message       = __( $default_email, "ws-comments-doi" );
		}
		$message = str_replace( '{{link}}', $link, $message );
		$message = str_replace( '{{name}}', $args["properties"]["comment_name"], $message );

		return wp_mail( $to, $subject, $message );
	}

	function filter_comment_moderation_message( $translated_text, $untranslated_text, $domain ) {
		// Check if the text to translate is the comment response text
		if ( $untranslated_text == 'Your comment is awaiting moderation.' || 'Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.' ) {
			$translated_text = __( "Your comment is not visible yet. Please verify your email though the link sent to you", "ws-comment-doi" );
			remove_filter( 'gettext', 'wpdocs_change_comment_notice', 10 );
			remove_action( 'template_redirect', [ $this, "maybe_add_custom_comment_moderation_message" ] );
		}

		return $translated_text;
	}

}
