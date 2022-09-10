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
    'click.n1ebieski/icore/web/scripts/ajax/newsletter@store',
    '.storeNewsletter, .store-newsletter',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $form = $element.parents('form');
        $form.btn = $form.find('.btn');
        $form.group = $form.find('.form-group');
        $form.input = $form.find('.form-control, .custom-control-input');

        $.ajax({
            url: $form.data('route'),
            method: 'post',
            data: $form.serialize(),
            dataType: 'json',
            beforeSend: function () {
                $element.loader('show');
                $('.invalid-feedback').remove();
                $('.valid-feedback').remove();
                $form.input.removeClass('is-valid');
                $form.input.removeClass('is-invalid');
            },
            complete: function () {
                $element.loader('hide');
                $form.input.addClass('is-valid');
            },
            success: function (response) {
                if (response.success) {
                    $form.find('[name="email"]').val('');
                    $form.find('[name="email"]').closest('.form-group').addMessage(response.success);
                }
            },
            error: function (response) {
                if (response.responseJSON.errors) {
                    $.each(response.responseJSON.errors, function (key, value) {
                        $form.find('[name="' + key + '"]').addClass('is-invalid');
                        $form.find('[name="' + key + '"]').closest('.form-group').addError({
                            id: key,
                            message: value
                        });
                    });
                }
            }
        });
    }
);
