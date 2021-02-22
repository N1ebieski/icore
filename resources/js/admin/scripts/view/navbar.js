(function($) {
    let c = $(window).scrollTop();
    let currentScrollTop = 0;
    let $navbar = $('.navbar');

    if ($navbar.data('autohide') === false) {
        return;
    }

    $(window).scroll(function () {
        if (!$('body').hasClass('modal-open')) {        
            var a = $(window).scrollTop();
            var b = $navbar.height() + 10;

            currentScrollTop = a;

            if (c < currentScrollTop && c > b) {
                $navbar.fadeOut();
            } else {
                $navbar.fadeIn();
            }
            c = currentScrollTop;
        }            
   });
})(jQuery);
