function ajaxCreateComment($element) {
    let $comment = $element.closest('[id^=comment]');

    $.ajax({
        url: $element.data('route'),
        method: 'get',
        beforeSend: function () {
            $element.prop('disabled', true);
            $comment.append($.getLoader('spinner-border', 'loader'));
        },
        complete: function () {
            $element.prop('disabled', false);
            $comment.find('.loader').remove();
            $comment.find('.captcha').recaptcha();
        },
        success: function (response) {
            $comment.children('div').append($.sanitize(response.view));
        },
        error: function (response) {
            if (response.responseJSON.message) {
                $comment.children('div').prepend($.getAlert('danger', response.responseJSON.message));
            }
        }
    });
}

jQuery(document).on('click', 'a.createComment, a.create-comment', function (e) {
    e.preventDefault();

    let $form = $(this).closest('[id^=comment]').find('form#createComment, form#create-comment');

    if ($form.length > 0) {
        $form.fadeToggle();
    } else {
        ajaxCreateComment($(this));
    }
});
