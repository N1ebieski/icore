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
    'click.n1ebieski/icore/web/scripts/view/lang@dropdown-toggle',
    'div#dropdown-multi-lang-toggle a',
    function (e) {
        e.preventDefault();

        let $element = $(this);
        let lang = $.sanitize($element.data('lang'));

        $.cookie("lang_toggle", lang, { 
            path: '/',
            expires: 365
        });

        window.location.replace($.sanitize($element.attr('href')));
    }
);
