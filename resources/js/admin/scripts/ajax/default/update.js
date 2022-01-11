$(document).on(
    'click.n1ebieski/icore/admin/scripts/ajax/default@update',
    '.update',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $form = $element.closest('.modal-content').find('form:visible');
        $form.btn = $form.find('.btn');
        $form.input = $form.find('.form-control');

        let data = new FormData($form[0]);
        data.append('_method', 'put');

        $.ajax({
            url: $form.data('route'),
            method: 'post',
            data: data,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
                $element.loader('show');
                $form.find('.invalid-feedback').remove();
                $form.input.removeClass('is-valid');
                $form.input.removeClass('is-invalid');
            },
            complete: function () {
                $element.loader('hide');
                $form.input.addClass('is-valid');
            },
            success: function (response) {
                let $row = $('#row' + $form.attr('data-id'));
                $row.html($.sanitize($(response.view).html()));

                $row.addClass('alert-primary');
                setTimeout(function () {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
                
                $('.modal').modal('hide');
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
