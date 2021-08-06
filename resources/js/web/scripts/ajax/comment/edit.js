jQuery(document).on('click', 'a.editComment, a.edit-comment', function (e) {
    e.preventDefault();

    let $element = $(this);

    let $comment = $element.closest('[id^=comment]');

    $.ajax({
        url: $element.data('route'),
        method: 'get',
        beforeSend: function () {
            $comment.children('div').hide();
            $comment.append($.getLoader('spinner-border', 'loader'));
        },
        complete: function () {
            $comment.find('.loader').remove();
        },
        success: function (response) {
            $comment.append($.sanitize(response.view));
        },
        error: function (response) {
            $comment.children('div').show();

            if (response.responseJSON.message) {
                $comment.children('div').prepend($.getAlert('danger', response.responseJSON.message));
            }
        }
    });
});

jQuery(document).on('click', 'button.editCommentCancel', function(e) {
    e.preventDefault();

    let $comment = $(this).closest('[id^=comment]');

    $comment.children('div').show();
    $comment.find('form#editComment').remove();
});
