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

$(document).on('ready.n1ebieski/icore/web/scripts/view/navbar@init', function () {
    let c = $(window).scrollTop();

    let currentScrollTop = 0;

    let $navbar = $('.menu.navbar');

    if ($navbar.data('autohide') === false) {
        return;
    }

    $(window).on('scroll', function () {
        if (!$('body').hasClass('modal-open')) {
            if ($('.trumbowyg-button-pane').css('position') === 'fixed') {
                $navbar.fadeOut();
                
                return;
            }

            let a = $(window).scrollTop();
            let b = $navbar.height() + 10;
    
            currentScrollTop = a;

            if (c < currentScrollTop && c > b) {
                $navbar.fadeOut();
            } else {
                $navbar.fadeIn();
            }
            c = currentScrollTop;
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
            $navbar.fadeOut();
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
