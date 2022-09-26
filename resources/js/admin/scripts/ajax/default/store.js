/*
 * NOTICE OF LICENSE
 * 
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 * 
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * 
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

$(document).on(
    'click.n1ebieski/icore/admin/scripts/ajax/default@store',
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
            success: function (response) {
                $form.closest('.modal').modal('hide');
                
                if (response.redirect.length) {
                    window.location.replace(response.redirect);

                    return;
                }

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
