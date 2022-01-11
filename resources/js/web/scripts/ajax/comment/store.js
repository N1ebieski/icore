$(document).on(
    'click.n1ebieski/icore/web/scripts/ajax/comment@store',
    '.storeComment, .store-comment',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $form = $element.closest('form');
        $form.btn = $form.find('.btn');
        $form.input = $form.find('.form-control');

        $.ajax({
            url: $form.data('route'),
            method: 'post',
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
                $form.find('.captcha').recaptcha();
                $form.find('.captcha').captcha();
            },
            success: function (response) {
                if (response.view) {
                    $form.closest('[id^=comment]').after($.sanitize(response.view));

                    let $comment = $form.closest('[id^=comment]').next('div');

                    $comment.addClass('alert-primary font-italic border-bottom');
                    setTimeout(function() {
                        $comment.removeClassStartingWith('alert-');
                    }, 5000);
                }

                if (response.success) {
                    $form.parent().addAlert({
                        message: response.success,
                        type: 'success'
                    });
                }

                if ($form.find('#parent_id').val() != 0) {
                    $form.remove();
                } else {
                    $form.find('#content').val('');
                }
            },
            error: function(response) {
                if (response.responseJSON.errors) {
                    $.each(response.responseJSON.errors, function(key, value) {
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
