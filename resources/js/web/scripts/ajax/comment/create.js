function ajaxCreateComment($element) {
    let $comment = $element.closest('[id^=comment]');
    $.ajax({
        url: $element.attr('data-route'),
        method: 'get',
        beforeSend: function() {
            $element.prop('disabled', true);
            $comment.append($.getLoader('spinner-border', 'loader'));
        },
        complete: function() {
            $element.prop('disabled', false);
            $comment.find('div.loader').remove();
            $comment.find('.captcha').recaptcha();
        },
        success: function(response) {
            $comment.children('div').append($.sanitize(response.view));
        },
        error: function(response) {
            if (response.responseJSON.message) {
                $comment.children('div').prepend($.getAlert(response.responseJSON.message, 'danger'));
            }
        }
    });
}

jQuery(document).on('click', 'a.createComment', function(e) {
    e.preventDefault();

    let $form = $(this).closest('[id^=comment]').find('form#createComment');

    if ($form.length > 0) {
        $form.fadeToggle();
    } else {
        ajaxCreateComment($(this));
    }
});
