$(document).on(
    'click.n1ebieski/icore/web/scripts/ajax/default@store',
    '.store',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $form = $element.closest('.modal-content').find('form:visible');
        $form.btn = $form.find('.btn');
        $form.input = $form.find('.form-control');

        $.ajax({
            url: $form.data('route'),
            method: 'post',
            data: new FormData($form[0]),
            processData: false,
            contentType: false,
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
            success: function () {
                $('.modal').modal('hide');
                
                window.location.reload();
            },
            error: function (response) {
                if (response.responseJSON.errors) {
                    let i = 0;

                    $.each(response.responseJSON.errors, function (key, value) {
                        if (!$form.find('#' + $.escapeSelector(key)).length) {
                            key = key.match(/([a-z_\-\.]+)(?:\.([\d]+)|)$/)[1];
                        }

                        $form.find('#' + $.escapeSelector(key)).addClass('is-invalid');
                        $form.find('#' + $.escapeSelector(key)).closest('.form-group').addError({
                            id: key,
                            message: value
                        });

                        if (i === 0 && $('#' + $.escapeSelector(key)).length) {
                            $form.parent().animate({
                                scrollTop: $form.parent().scrollTop() + $form.find('#' + $.escapeSelector(key)).closest('.form-group').position().top
                            }, 1000);
                        }

                        i++;
                    });

                    return;                    
                }
                
                if (response.responseJSON.message) {
                    $('body').addToast({
                        title: response.responseJSON.message,
                        type: 'danger'
                    });
                }
            }
        });
    }
);
