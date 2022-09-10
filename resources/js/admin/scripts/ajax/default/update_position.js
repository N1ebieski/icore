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
    'click.n1ebieski/icore/admin/scripts/ajax/default@updatePosition',
    '.updatePosition, .update-position',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $form = $element.closest('.modal-content').find('form');

        $.ajax({
            url: $form.data('route'),
            method: 'patch',
            data: {
                position: $form.find('#position').val(),
            },
            beforeSend: function () {
                $element.loader('show');
            },
            complete: function () {
                $element.loader('hide');
            },
            success: function (response) {
                $form.closest('.modal').modal('hide');

                $.each(response.siblings, function (key, value) {
                    let $rowSibling = $('#row' + key);

                    if ($rowSibling.length) {
                        $rowSibling.find('#position').text(value + 1);

                        $rowSibling.addClass('alert-primary');
                        setTimeout(function () {
                            $rowSibling.removeClassStartingWith('alert-');
                        }, 5000);
                    }
                });
            }
        });
    }
);
