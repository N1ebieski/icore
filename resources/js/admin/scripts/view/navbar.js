(function($) {
    let c, currentScrollTop = 0;
    let $navbar = $('.navbar');

    $(window).scroll(function() {
        var a = $(window).scrollTop();
        var b = $navbar.height()+10;

        currentScrollTop = a;

        if (c < currentScrollTop && c > b) {
            $navbar.fadeOut();
        } else {
            $navbar.fadeIn();
        }
        c = currentScrollTop;
   });
})(jQuery);
