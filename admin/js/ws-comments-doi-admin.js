/// <reference path="./notifyjs/snackbar.ts"/>
import snackbar from "./notifyjs/snackbar.min.js";

(function ($) {
    function wsCommentsDoiHandleAddVerifiedEmail(event, $input) {
        event.preventDefault();
        const $email = $input.val();
        _addVerifiedEmail($email)
        $input.val("")
    }

    function _addVerifiedEmail(email) {
        var data = {
            'action': 'ws_comments_doi_add_verified_email', // This should match with the wp_ajax_ hook
            'email': email,
            'ws_comments_doi_nonce': wsCommentsDoiConfig.security
        };

        $.post(wsCommentsDoiConfig.ajax_url, data, function (response) {
            // Handle the response here
            if (response.success) {
                const addedMessage = wsCommentsDoiConfig.added_message
                sendMessage(`<b>${email}</b> ${addedMessage}`)
                $('#ws_comment_doi_verified_emails_list').replaceWith(response.data.verified_emails)
                _addDeleteButtonEventHandlers()
            } else {
                sendMessage(`${wsCommentsDoiConfig.error_message}: ${response?.data?.message ?? ""}`, "error")
            }
        });
    }

    function wsCommentDoiHandleDeleteAllVerifiedEmails(event) {
        event.preventDefault()
        sendMessage(wsCommentsDoiConfig.all_deleted_confirmation, "question", [{
            label: wsCommentsDoiConfig.all_delete_confirmation_button,
            callback: _wsCommentDoiHandleDeleteAllVerifiedEmails
        }])
    }

    function _wsCommentDoiHandleDeleteAllVerifiedEmails() {

        const data = {
            'action': 'ws_comments_doi_delete_all_verified_emails', // This should match with the wp_ajax_ hook
            'ws_comments_doi_nonce': wsCommentsDoiConfig.security
        };

        $.post(wsCommentsDoiConfig.ajax_url, data, function (response) {
            // Handle the response here
            if (response.success) {
                sendMessage(wsCommentsDoiConfig.all_deleted_message)
                $('#ws_comment_doi_verified_emails_list').replaceWith(response.data.verified_emails)
            } else {
                sendMessage(`${wsCommentsDoiConfig.error_message}: ${response?.data?.message ?? ""}`, "error")
            }
        });
    }

    function wsCommentDoiHandleDeleteVerifiedEmail(event, $button) {
        event.preventDefault()
        console.log($button)
        const email = $button.data('email')
        const data = {
            'action': 'ws_comments_doi_delete_verified_email', // This should match with the wp_ajax_ hook
            'ws_comments_doi_nonce': wsCommentsDoiConfig.security,
            'email': email
        };

        $.post(wsCommentsDoiConfig.ajax_url, data, function (response) {
            // Handle the response here
            if (response.success) {
                sendMessage(`<b>${email}</b> ${wsCommentsDoiConfig.deleted_message}`, "success", [
                    {
                        label: wsCommentsDoiConfig.undo_message,
                        callback: function () {
                            _addVerifiedEmail(email)
                        }
                    }
                ])
                $('#ws_comment_doi_verified_emails_list').replaceWith($(response.data.verified_emails))
                _addDeleteButtonEventHandlers();
            } else {
                sendMessage(`${wsCommentsDoiConfig.error_message}: ${response?.data?.message ?? ""}`, "error")            }
        });
    }

    jQuery(document).ready(function ($) {
        const $button = $('#ws_comments_doi_new_email_submit')
        const $input = $('#ws_comments_doi_new_email')
        const $deleteAllButton = $('#ws_comments_doi_delete_all_verified_emails')

        $button.click(function (e) {
            wsCommentsDoiHandleAddVerifiedEmail(e, $input)
        });
        $button.keydown(function (e) {
            if (e.key === "Enter") {
                wsCommentsDoiHandleAddVerifiedEmail(e, $input)
            }
        })
        $input.keydown(function (e) {
            if (e.key === "Enter") {
                wsCommentsDoiHandleAddVerifiedEmail(e, $input)
            }
        })

        $deleteAllButton.click(wsCommentDoiHandleDeleteAllVerifiedEmails)
        $deleteAllButton.keydown(function (e) {
            if (e.key === "Enter") {
                wsCommentDoiHandleDeleteAllVerifiedEmails(e)
            }
        })

        _addDeleteButtonEventHandlers()
    });

    function _addDeleteButtonEventHandlers() {
        const $deleteButtons = $('.ws_comments_doi_delete_email')
        $deleteButtons.click(function (e) {
            wsCommentDoiHandleDeleteVerifiedEmail(e, $(this))
        })
        $deleteButtons.keydown(function (e) {
            if (e.key === "Enter") {
                wsCommentDoiHandleDeleteVerifiedEmail(e, $(this))
            }
        })
    }

    function sendMessage(message = "", type = "success", buttons = []) {
        snackbar({
            message: `${_renderImage(type)} ${message}`,
            duration: 15,
            buttons: buttons
        })
    }

    function _renderImage(state = "success") {
        if (state === "success") {
            const image = wsCommentsDoiConfig.success_image
            return `<img src="${image}" alt="success" width=30 height=30 role="presentation"/>`
        } else if (state === "question") {
            const image = wsCommentsDoiConfig.question_image
            return `<img src="${image}" alt="success" width=30 height=30 role="presentation" style="margin-bottom:-5px;"/>`
        } else {
            const image = wsCommentsDoiConfig.error_image
            return `<img src="${image}" alt="error" width="30" height="30" role="presentation" style="margin-bottom:-5px;"/>`
        }
    }
})(jQuery)
