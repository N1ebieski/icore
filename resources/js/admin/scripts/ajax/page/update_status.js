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
    'click.n1ebieski/icore/admin/scripts/ajax/page@updateStatus',
    '.statusPage, .status-page',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $row = $element.closest('[id^=row]');
        $row.btnGroup = $row.find('.responsive-btn-group');
        $row.btn0 = $row.find('button[data-status="0"]');
        $row.btn1 = $row.find('button[data-status="1"]');

        $.ajax({
            url: $element.data('route'),
            method: 'patch',
            data: {
                status: $element.data('status'),
            },
            beforeSend: function () {
                $row.btnGroup.addClass('disabled');
                $element.loader('show');
            },
            success: function (response) {
                $element.loader('hide');

                $row.html($.sanitize($(response.view).html()));

                if (response.status == 1) {
                    $row.btnGroup.removeClass('disabled');
                    $row.btn1.prop('disabled', true);
                    $row.btn0.prop('disabled', false);

                    $row.addClass('alert-success');
                    setTimeout(function () {
                        $row.removeClassStartingWith('alert-');
                    }, 5000);

                    $.each(response.ancestors, function (key, value) {
                        let $rowAncestor = $('#row' + value);

                        if ($rowAncestor.length) {
                            $rowAncestor.find('button[data-status="1"]').prop('disabled', true);
                            $rowAncestor.find('button[data-status="0"]').prop('disabled', false);

                            $rowAncestor.addClass('alert-success');
                            setTimeout(function () {
                                $rowAncestor.removeClassStartingWith('alert-');
                            }, 5000);
                        }
                    });
                }

                if (response.status == 0) {
                    $row.btnGroup.removeClass('disabled');
                    $row.btn0.prop('disabled', true);
                    $row.btn1.prop('disabled', false);

                    $row.addClass('alert-warning');
                    setTimeout(function () {
                        $row.removeClassStartingWith('alert-');
                    }, 5000);

                    $.each(response.descendants, function (key, value) {
                        let $rowDescendant = $('#row' + value);

                        if ($rowDescendant.length) {
                            $rowDescendant.find('button[data-status="0"]').prop('disabled', true);
                            $rowDescendant.find('button[data-status="1"]').prop('disabled', false);

                            $rowDescendant.addClass('alert-warning');
                            setTimeout(function () {
                                $rowDescendant.removeClassStartingWith('alert-');
                            }, 5000);
                        }
                    });
                }
            }
        });
    }
);
