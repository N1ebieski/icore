$(document).on(
    'click.n1ebieski/icore/web/scripts/ajax/comment@edit',
    'a.editComment, a.edit-comment',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $comment = $element.closest('[id^=comment]');

        $.ajax({
            url: $element.data('route'),
            method: 'get',
            beforeSend: function () {
                $comment.children('div').hide();
                $comment.addLoader({
                    type: 'spinner-border',
                    class: 'loader'
                });
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
                    $comment.parent().addAlert(response.responseJSON.message);
                }
            }
        });
    }
);

$(document).on(
    'click.n1ebieski/icore/web/scripts/ajax/comment@cancel',
    'button.editCommentCancel, button.edit-comment-cancel',
    function(e) {
        e.preventDefault();

        let $comment = $(this).closest('[id^=comment]');

        $comment.children('div').show();
        $comment.find('form#editComment, form#edit-comment').remove();
    }
);
