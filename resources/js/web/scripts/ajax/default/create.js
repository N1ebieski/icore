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
    'click.n1ebieski/icore/web/scripts/ajax/default@create',
    '.create',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $modal = $($element.data('target'));
        $modal.body = $($element.data('target')).find('.modal-body');
        $modal.footer = $($element.data('target')).find('.modal-footer');
        $modal.content = $($element.data('target')).find('.modal-content');

        $modal.on('show.bs.modal', function () {
            $(this).find('.modal-body').empty();
            $(this).find('.modal-footer').empty();
        });

        $modal.on('hidden.bs.modal', function () {
            $(this).find('.modal-body').empty();
            $(this).find('.modal-footer').empty();
        });

        $.ajax({
            url: $element.data('route'),
            method: 'get',
            beforeSend: function () {
                $modal.body.addLoader('spinner-grow');
            },
            complete: function () {
                $modal.body.find('.loader-absolute').remove();
            },
            success: function (response) {
                $modal.content.html($.sanitize($(response.view).find('.modal-content').html()));
            },
            error: function (response) {
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
