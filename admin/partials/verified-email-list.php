<ul id="ws_comment_doi_verified_emails_list">
	<?php
	/** @var array $verified_emails */
	foreach ( $verified_emails as $email ) {
		// Output each email with a delete button
		// Use wp_nonce_url for security
		$delete_url = wp_nonce_url( add_query_arg( array(
			'action' => 'ws_comments_doi_delete_verified_email',
			'email'  => urlencode( $email ),
		), admin_url( 'options-discussion.php' ) ), 'ws_comments_doi_delete_verified_email' );
		?>
        <li>
			<?php echo esc_html( $email ); ?>
            <button data-email="<?php echo esc_attr( $email ); ?>" class="ws_comments_doi_delete_email">
                <?php _e("Delete", "default") ?>
            </button>
        </li>
		<?php
	}
	?>
</ul>
