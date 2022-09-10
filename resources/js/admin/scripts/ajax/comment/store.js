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
    'click.n1ebieski/icore/admin/scripts/ajax/comment@store',
    '.storeComment, .store-comment',
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
                $form.input.addClass('is-valid');
            },
            success: function (response) {
                let $row = $('#row' + $form.data('id'));
                $row.after($.sanitize(response.view));

                let $rowNext = $row.next();
                $rowNext.addClass('alert-primary font-italic');
                setTimeout(function () {
                    $rowNext.removeClassStartingWith('alert-');
                }, 5000);

                $('.modal').modal('hide');
            },
            error: function (response) {
                $.each(response.responseJSON.errors, function (key, value) {
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
