$(document).on('scroll.n1ebieski/icore/admin/scripts/view/scroll_to_top@init', function () {
    let scrollDistance = $(this).scrollTop();

    if (scrollDistance > 100) {
        $('.scroll-to-top').fadeIn();
    } else {
        $('.scroll-to-top').fadeOut();
    }
});

$(document).on(
    'click.n1ebieski/icore/admin/scripts/view/scroll_to_top@scroll',
    'a.scroll-to-top',
    function (event) {
        $('html, body').stop().animate({
            scrollTop: (0)
        }, 1000, 'easeInOutExpo');
        
        event.preventDefault();
    }
);
