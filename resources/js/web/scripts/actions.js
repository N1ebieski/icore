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

$(function () {
    $(document).trigger('ready');
    $(document).trigger('readyAndAjax');
});

$(document).ajaxComplete(function () {
    $(document).trigger('readyAndAjax');
});

$(document).on('readyAndAjax.n1ebieski/icore/web/scripts/actions@enter', function () {
    $('form').find('input, select')
        .on('keypress', function (e) {
            if (e.which == 13) {
                e.preventDefault();
                
                return false;
            }
        });
});
