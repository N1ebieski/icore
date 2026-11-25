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

let setNavbarVisibility = function ($navbar, visible) {
    if ($navbar.data('autohide-visible') === visible) {
        return;
    }

    $navbar.data('autohide-visible', visible).stop(true, false);

    if (visible) {
        $navbar.fadeIn();
    } else {
        $navbar.fadeOut();
    }
};

$(document).on('ready.n1ebieski/icore/admin/scripts/view/navbar@init', function () {
    let $navbar = $('.navbar');

    if ($navbar.data('autohide') === false) {
        return;
    }

    let lastScrollTop = Math.max(0, $(window).scrollTop());
    let navbarHeight = $navbar.outerHeight() || $navbar.height();
    let directionThreshold = 4;

    $navbar.data('autohide-visible', $navbar.is(':visible'));

    $(window).on('scroll', function () {
        if (!$('body').hasClass('modal-open')) {
            if ($('.trumbowyg-button-pane').css('position') === 'fixed') {
                setNavbarVisibility($navbar, false);

                return;
            }

            let scrollTop = Math.max(0, $(window).scrollTop());
            let scrollDelta = scrollTop - lastScrollTop;

            if ($navbar.is(':visible')) {
                navbarHeight = $navbar.outerHeight() || navbarHeight;
            }

            if (scrollTop <= navbarHeight + 10) {
                setNavbarVisibility($navbar, true);
            } else if (scrollDelta >= directionThreshold) {
                setNavbarVisibility($navbar, false);
            } else if (scrollDelta <= -directionThreshold) {
                setNavbarVisibility($navbar, true);
            } else {
                return;
            }

            lastScrollTop = scrollTop;
        }
    });
});
