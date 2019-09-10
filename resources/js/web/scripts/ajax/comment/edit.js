jQuery(document).on('click', 'a.editComment', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $comment = $element.closest('[id^=comment]');

    $.ajax({
        url: $element.attr('data-route'),
        method: 'get',
        beforeSend: function() {
            $comment.children('div').hide();
            $comment.append($.getLoader('spinner-border', 'loader'));
        },
        complete: function() {
            $comment.find('div.loader').remove();
        },
        success: function(response) {
            $comment.append($.sanitize(response.view));
        },
        error: function(response) {
            $comment.children('div').show();
            if (response.responseJSON.message) {
                $comment.children('div').prepend($.getAlert(response.responseJSON.message, 'danger'));
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
