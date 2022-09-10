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
    'click.n1ebieski/icore/admin/scripts/ajax/page@clear',
    '.clearReport, .clear-report',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        $.ajax({
            url: $element.data('route'),
            method: 'delete',
            beforeSend: function () {
                $element.loader('show');
            },
            complete: function () {
                $element.loader('hide');
            },
            success: function (response) {
                let $row = $('#row' + $element.attr('data-id'));

                $row.html($.sanitize($(response.view).html()));

                $row.addClass('alert-primary');
                setTimeout(function () {
                    $row.removeClassStartingWith('alert-');
                }, 5000);

                $('.modal').modal('hide');
            }
        });
    }
);
