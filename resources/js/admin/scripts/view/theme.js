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
    'click.n1ebieski/icore/admin/scripts/view/theme@toggle',
    'div#themeToggle button, div#theme-toggle button',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        if ($element.hasClass('btn-light')) {
            $.cookie("theme_toggle", 'light', { 
                path: '/',
                expires: 365
            });
        }

        if ($element.hasClass('btn-dark')) {
            $.cookie("theme_toggle", 'dark', { 
                path: '/',
                expires: 365
            });
        }

        window.location.reload();
    }
);

$(document).on(
    'click.n1ebieski/icore/web/scripts/view/theme@dropdown-toggle',
    'div#dropdown-multi-theme-toggle a',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        $.cookie("theme_toggle", $.sanitize($element.data('theme')), { 
            path: '/',
            expires: 365
        });

        window.location.reload();
    }
);
