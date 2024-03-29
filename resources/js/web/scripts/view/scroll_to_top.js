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

$(document).on('scroll.n1ebieski/icore/web/scripts/view/scroll_to_top@init', function () {
    let scrollDistance = $(this).scrollTop();

    if (scrollDistance > 100) {
        $('.scroll-to-top').fadeIn();
    } else {
        $('.scroll-to-top').fadeOut();
    }
});

$(document).on(
    'click.n1ebieski/icore/web/scripts/view/scroll_to_top@scroll',
    'a.scroll-to-top',
    function (event) {
        $('html, body').stop().animate({
            scrollTop: (0)
        }, 1000, 'easeInOutExpo');
        
        event.preventDefault();
    }
);
