<?php
/**
 * The admin-settings specific functionality of the plugin.
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
 * @package    Ws_Comments_Doi
 * @subpackage Ws_Comments_Doi/admin
 * @author     Robin | Westsite <loeseke@westsite-webdesign.de>
 */
class Ws_Comments_Doi_Admin_Settings {
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

	function add_custom_discussion_settings() {
		wp_enqueue_script( "{$this->plugin_name}_admin" );
		wp_enqueue_style( "{$this->plugin_name}_admin" );

		wp_enqueue_style( 'notifyjs' );
		// Register a new setting for the discussions page
		register_setting( 'discussion', 'ws_comments_doi_subject_text', 'sanitize_text_field' );
		register_setting( 'discussion', 'ws_comments_doi_email_content', 'wp_kses_post' );
		register_setting( 'discussion', 'ws_comments_doi_email_checkbox', 'intval' );
		register_setting( 'discussion', 'ws_comments_doi_unverified_note', 'sanitize_text_field' );

		// Add a new section to the discussions page
		add_settings_section(
			'ws_comments_doi_section',
			__( 'Comments Double Opt-In settings', "ws-comments-doi" ),
			[ $this, 'ws_comments_doi_settings_section' ],
			'discussion'
		);

		// Add a new field for the checkbox
		add_settings_field(
			'ws_comments_doi_enable_checkbox',
			__( 'Enable Comments DOI', "ws-comments-doi" ),
			[ $this, 'enable_checkbox_callback' ],
			'discussion',
			'ws_comments_doi_section'
		);

		// Add a new field for the WYSIWYG editor
		add_settings_field(
			'ws_comments_doi_subject_text',
			__( 'Subject of Verification Email', "ws-comments-doi" ),
			[ $this, 'ws_comments_doi_subject_text' ],
			'discussion',
			'ws_comments_doi_section'
		);

		// Add a new field for the WYSIWYG editor
		add_settings_field(
			'ws_comments_doi_email_editor',
			__( 'Verification email content', "ws-comments-doi" ),
			[ $this, 'email_editor_callback' ],
			'discussion',
			'ws_comments_doi_section'
		);

		// Add a new field for the unverified email note
		add_settings_field(
			'ws_comments_doi_unverified_note',
			__( 'Unverified Email Note', "ws-comments-doi" ),
			[ $this, 'unverified_email_note_callback' ],
			'discussion',
			'ws_comments_doi_section'
		);

        add_settings_field(
                'ws_comments_doi_send_again_link_text',
            __("Send Again Link Text", "ws-comments-doi"),
            [$this, 'send_again_link_text_callback'],
            'discussion',
            'ws_comments_doi_section'
        );

        add_settings_field(
                'ws_comments_doi_send_again_success_message',
            __("Send Again Success Message", "ws-comments-doi"),
            [$this, "send_again_success_message"],
            'discussion',
            'ws_comments_doi_section'
        );

		add_settings_field(
			'ws_comments_doi_verified_emails',
			__( 'Verified Emails', "ws-comment-doi" ),
			[ $this, 'manage_verified_emails_callback' ],
			'discussion',
			'ws_comments_doi_section' // Change this if you want to add it to a different section
		);
	}

	function ws_comments_doi_settings_section() {
		echo '<p id="doi_section">Customize the verification email sent to users when they post a comment.</p>';
	}

	function email_editor_callback() {
		$content = get_option( 'ws_comments_doi_email_content' );
		if ( ! $content ) {
			$content = <<<EOT
Hello {{name}},

Thank you for your recent comment on our website.

To ensure the authenticity and security of submissions, we require a quick email verification. Your comment will be posted on our site once your email address is confirmed.

Please click the link below to verify your email and complete the process:

{{link}}
If you did not submit a comment or are unaware of this email, please disregard this message and do not click the link.

Thank you for your participation and understanding.

Best regards!
EOT;
		}
		?>
        <div class="placeholder-descriptions" style="margin-bottom: 20px;">
            <p><strong><?php _e( "Placeholder Descriptions", "ws-comments-doi" ) ?>:</strong></p>
            <p>
                <strong>{{name}}: </strong><?php _e( "This will be replaced with the commentators name.", "ws-comments-doi" ) ?>
            </p>
            <p>
                <strong>{{link}}: </strong> <?php _e( "This will be replaced with the email verification link.", "s-comments-doi" ) ?>
            </p>
        </div>
		<?php
		wp_editor( __( $content, "ws-comments-doi" ), 'ws_comments_doi_email_content', array( 'textarea_name' => 'ws_comments_doi_email_content' ) );
	}

	function unverified_email_note_callback() {
		$unverified_note = get_option( 'ws_comments_doi_unverified_note',
			__( "Your comment is not visible yet. Please verify your email through the link sent to you", "ws-comment-doi" ) );
		?>
        <input type="text" id="ws_comments_doi_unverified_note"
               name="ws_comments_doi_unverified_note"
               value="<?php echo esc_attr( $unverified_note ); ?>"
               class="large-text"/>
        <label for="ws_comments_doi_unverified_note" class="description">
			<?php _e( "Enter the note text that viewers will see when posting a comment using an unverified email.", "ws-comment-doi" ); ?>
        </label>
		<?php
	}

	function send_again_link_text_callback() {
		$unverified_note = get_option( 'ws_comments_doi_send_again_link_text',
			__( "Send confirmation email again", "ws-comment-doi" ) );
		?>
        <input type="text" id="ws_comments_doi_send_again_link_text"
               name="ws_comments_doi_send_again_link_text"
               value="<?php echo esc_attr( $unverified_note ); ?>"
               class="large-text"/>
        <label for="ws_comments_doi_send_again_link_text" class="description">
			<?php _e( "Enter the link text of send again link visible after a comment was posted from an unverified email", "ws-comment-doi" ); ?>
        </label>
		<?php
	}

    function send_again_success_message() {
		$value = get_option( 'ws_comments_doi_send_again_success_message',
			__( "Confirmation mail sent again", "ws-comment-doi" ) );
		?>
        <input type="text" id="ws_comments_doi_send_again_success_message"
               name="ws_comments_doi_send_again_success_message"
               value="<?php echo esc_attr( $value ); ?>"
               class="large-text"/>
        <label for="ws_comments_doi_send_again_success_message" class="description">
			<?php _e( "Enter the message shown after confirmation mail was sent again", "ws-comment-doi" ); ?>
        </label>
		<?php
	}

	/**
	 * @return void
	 * @since 1.0.0
	 */
	function ws_comments_doi_subject_text() {
		$site_title   = wp_specialchars_decode(
			get_bloginfo( 'name' ),
			ENT_QUOTES
		);
		$subject_text = get_option( 'ws_comments_doi_subject_text',
			__( "{$site_title}: Verify your email before commenting", "ws-comments-doi" ) );
		?>
        <input type="text" id="ws_comments_doi_subject_text"
               name="ws_comments_doi_subject_text"
               value="<?php echo esc_attr( $subject_text ) ?>"
               class="large-text"/>
        <label for="ws_comments_doi_subject_text">
			<?php echo __( "Specify the subject for the verification mails", "ws-comment-doi" ) ?>
        </label>
		<?php
	}

	function enable_checkbox_callback() {
		$checked = get_option( 'ws_comments_doi_email_checkbox' );
		?>
        <input type="checkbox" id="ws_comments_doi_email_checkbox"
               name="ws_comments_doi_email_checkbox"
               value="1" <?php checked( 1, $checked ) ?> />
        <label for="ws_comments_doi_email_checkbox">
			<?php echo __( "Require not logged in users to verify their email before their first comment is posted publicaly", "ws-comment-doi" ) ?>
        </label>
		<?php
	}

	function manage_verified_emails_callback() {
// Get the verified emails from the database
		$verified_emails = get_option( 'verified_emails', array() );
		$total_emails    = count( $verified_emails );

// Display the emails with pagination
		$delete_all_url = wp_nonce_url( add_query_arg( 'action', 'ws_comments_doi_delete_all_verified_emails' ), 'ws_comments_doi_delete_all_verified_emails' ); ?>
        <div>
            <button id="ws_comments_doi_delete_all_verified_emails" class="button">
				<?php echo esc_html__( 'Delete All Verified Emails', 'text-domain' ) ?>
            </button>
			<?php
			include plugin_dir_path( __FILE__ ) . 'partials/verified-email-list.php';
			include plugin_dir_path( __FILE__ ) . 'partials/new-email-form.php';
			?>
        </div> <?php
	}
}