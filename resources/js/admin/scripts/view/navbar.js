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
