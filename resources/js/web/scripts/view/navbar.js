jQuery(document).ready(function() {
    let c, currentScrollTop = 0;
    let $navbar = $('.navbar');

    $(window).scroll(function () {
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
});

jQuery(document).on('click', ".modal-backdrop, #navbarToggle", function(e) {
    e.preventDefault();

    if ($('.modal-backdrop').length) {
        $('.navbar-collapse').collapse('hide');
        $('.modal-backdrop').fadeOut('slow', function() {
            $(this).remove();
        });
        $('body').removeClass('modal-open');
    } else {
        $('.navbar-collapse').collapse('show');
        $('<div class="modal-backdrop show z-900"></div>').appendTo('body').hide().fadeIn();
        $('body').addClass('modal-open');
    }
});
