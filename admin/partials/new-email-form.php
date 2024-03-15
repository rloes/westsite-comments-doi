<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/rloes
 * @since      1.0.0
 *
 * @package    Ws_Comments_Doi
 * @subpackage Ws_Comments_Doi/admin/partials
 */
?>

<label for="ws_comments_doi_new_email"><?php echo __( "Add verified email", "ws-comments-doi" ) ?></label>
<input type="email" id="ws_comments_doi_new_email" name="new_verified_email">
<button id="ws_comments_doi_new_email_submit" class="button"><?php _e("Add", "ws-comments-doi") ?></button>