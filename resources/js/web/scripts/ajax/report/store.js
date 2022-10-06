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
    'click.n1ebieski/icore/web/scripts/ajax/report@store',
    '.storeReport, .store-report',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $form = $element.closest('.modal-content').find('form');
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
                $form.find('.captcha').recaptcha();
                $form.find('.captcha').captcha();                
                $form.input.addClass('is-valid');
            },
            success: function (response) {
                $('.modal').modal('hide');

                $('body').addToast(response.success);
            },
            error: function (response) {
                let errors = response.responseJSON;

                $.each(errors.errors, function (key, value) {
                    $form.find('#' + $.escapeSelector(key)).addClass('is-invalid');
                    $form.find('#' + $.escapeSelector(key)).closest('.form-group').addError({
                        id: key,
                        message: value
                    });
                });
            }
        });
    }
);
