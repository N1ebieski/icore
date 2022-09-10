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

$(document).on('ready.n1ebieski/icore/admin/scripts/view/navbar@init', function () {
    let c = $(window).scrollTop();
    let currentScrollTop = 0;
    let $navbar = $('.navbar');

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
