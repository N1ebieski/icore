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

$(document).on('ready.n1ebieski/icore/web/scripts/view/navbar@init', function () {
    let $navbar = $('.menu.navbar');

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

$(document).on('ready.n1ebieski/icore/web/scripts/view/navbar@hashtag', function () {
    let hash = window.location.hash;

    let $navbar = $('.menu.navbar');

    if ($navbar.data('autohide') === false) {
        return;
    }

    if (hash.length) {
        let a = $(window).scrollTop();
        let b = $navbar.height() + 10;

        if (a > b) {
            setNavbarVisibility($navbar, false);
        }
    }
});

$(document).on(
    'click.n1ebieski/icore/web/scripts/view/navbar@toggle',
    ".modal-backdrop, #navbarToggle, #navbar-toggle",
    function (e) {
        e.preventDefault();

        if ($('.modal-backdrop').length) {
            $('.navbar-collapse').collapse('hide');
            $('.modal-backdrop').fadeOut('slow', function () {
                $(this).remove();
            });
            $('body').removeClass('modal-open');
        } else {
            $('.navbar-collapse').collapse('show');
            $('<div class="modal-backdrop show z-900"></div>').appendTo('body').hide().fadeIn();
            $('body').addClass('modal-open');
        }
    }
);
