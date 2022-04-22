$(document).on(
    'click.n1ebieski/icore/web/scripts/ajax/comment@update',
    '.updateComment, .update-comment',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $form = $element.closest('form');
        $form.btn = $form.find('.btn');
        $form.input = $form.find('.form-control');

        $.ajax({
            url: $form.data('route'),
            method: 'put',
            data: $form.serialize(),
            dataType: 'json',
            beforeSend: function () {
                $element.loader('show');
                $('.invalid-feedback').remove();
                $form.input.removeClass('is-valid');
                $form.input.removeClass('is-invalid');
            },
            complete: function () {
                $element.loader('hide');
                $form.input.addClass('is-valid');
            },
            success: function (response) {
                let $comment = $form.closest('[id^=comment]');

                $comment.html($.sanitize($(response.view).children().html()));

                $comment.addClass('alert-primary');
                
                setTimeout(function () {
                    $comment.removeClassStartingWith('alert-');
                }, 5000);
            },
            error: function (response) {
                if (response.responseJSON.errors) {
                    $.each(response.responseJSON.errors, function (key, value) {
                        $form.find('#' + $.escapeSelector(key)).addClass('is-invalid');
                        $form.find('#' + $.escapeSelector(key)).closest('.form-group').addError({
                            id: key,
                            message: value
                        });
                    });
                    return;
                }

                if (response.responseJSON.message) {
                    $form.addAlert(response.responseJSON.message);
                }
            }
        });
    }
);
