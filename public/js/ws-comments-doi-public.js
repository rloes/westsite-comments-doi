import snackbar from "../../admin/js/notifyjs/snackbar.min.js";

document.addEventListener('DOMContentLoaded', function () {
    const elements = document.querySelectorAll('.comment-awaiting-moderation');
    const url = new URL(window.location.href);
    if (url.searchParams.has('send_again')) {
        // Remove the 'send_again' parameter
        url.searchParams.delete('send_again');
        // Update the URL without reloading the page
        window.history.pushState({}, '', url.href);
        if (wsCommentsDoiConfig.email_sent) {
            /*const commentSelector = `#comment-${url.searchParams.get('unapproved')}>article`
            const message = document.createElement('div');
            message.textContent = " " + wsCommentsDoiConfig.succes_message_text;
            message.classList.add('confirmation-message')
            // Append to body
            //document.querySelector(commentSelector).parentNode.insertBefore(message, document.querySelector(commentSelector).nextSibling);
            document.querySelector(commentSelector).appendChild(message);

            // Remove the message after 5 seconds
            setTimeout(function () {
                message.remove();
            }, 5000);*/
            snackbar({
                message: _renderImage()+wsCommentsDoiConfig.succes_message_text,
                duration: 10
            })
        }else {
            snackbar({
                message: _renderImage("error"),
                duration: 10
            })
        }
    }
    url.searchParams.set('send_again', '1');
    elements.forEach(function (element) {
        element.textContent = wsCommentsDoiConfig.moderation_text;

        // Create the link element
        const link = document.createElement('a');
        link.href = url.href;
        link.textContent = wsCommentsDoiConfig.link_text; // Text for the link

        // Append the link to the comment element
        element.appendChild(link);
    });
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
});
