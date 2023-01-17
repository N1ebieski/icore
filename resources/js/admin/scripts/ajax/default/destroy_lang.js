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
    'click.n1ebieski/icore/admin/scripts/ajax/default@destroyLang',
    '.destroy-lang',
    function (e) {
        e.preventDefault();

        let $element = $(this);
        
        let $row = $('#row' + $element.data('id'));

        $.ajax({
            url: $element.data('route'),
            method: 'delete',
            beforeSend: function () {
                $row.find('.responsive-btn-group').addClass('disabled');
                $row.find('[data-btn-ok-class*="destroy-lang"]').loader('show');
            },
            complete: function () {
                $row.find('[data-btn-ok-class*="destroy-lang]').loader('hide');
            },
            success: function () {
                $row.fadeOut('slow');
            }
        });
    }
);
