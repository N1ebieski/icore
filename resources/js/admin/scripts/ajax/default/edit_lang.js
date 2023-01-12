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
    'change.n1ebieski/icore/admin/scripts/ajax/default@editLang',
    'select.edit-lang',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        if ($element.data('loaded') !== true) {
            return;
        }

        let $modal = $($element.data('target'));
        $modal.body = $($element.data('target')).find('.modal-body');
        $modal.footer = $($element.data('target')).find('.modal-footer');
        $modal.content = $($element.data('target')).find('.modal-content');

        $modal.body.empty();
        $modal.footer.empty();

        $.ajax({
            url: $element.val(),
            method: 'get',
            beforeSend: function () {
                $modal.body.addLoader('spinner-grow');
            },
            complete: function () {
                $modal.body.find('.loader-absolute').remove();
            },
            success: function (response) {
                $modal.content.html($.sanitize($(response.view).find('.modal-content').html()));
            }
        });
    }
);