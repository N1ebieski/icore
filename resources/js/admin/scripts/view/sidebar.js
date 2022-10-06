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
    'click.n1ebieski/icore/admin/scripts/view/sidebar@init',
    ".modal-backdrop, #sidebar-toggle",
    function (e) {
        e.preventDefault();

        // For larger resolutions, the sidebar is always visible (toggled or not)
        if (window.innerWidth >= 768) {
            $(".sidebar").toggleClass("toggled");
            if ($("ul.sidebar").hasClass("toggled")) {
                $.cookie("sidebar_toggle", 1, { path: '/admin' });
            } else {
                $.cookie("sidebar_toggle", 0, { path: '/admin' });
            }
        }
        // For smaller resolutions, the sidebar is collapse with body backdrop
        else {
            $(".sidebar").removeClass("toggled");
            if ($('.modal-backdrop').length) {
                $('.modal-backdrop').fadeOut('slow', function () {
                    $(this).remove();
                });
                $(".sidebar").removeClass("show");
                $('body').removeClass('modal-open');
            } else {
                $('<div class="modal-backdrop show z-900"></div>').appendTo('body').hide().fadeIn();
                $(".sidebar").addClass("show");
                $('body').addClass('modal-open');
            }
        }
    }
);
