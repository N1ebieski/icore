$(document).on('click', ".modal-backdrop, #sidebarToggle", function(e) {
    e.preventDefault();

    // For larger resolutions, the sidebar is always visible (toggled or not)
    if (window.innerWidth >= 768) {
        $(".sidebar").toggleClass("toggled");
        if ($("ul.sidebar").hasClass("toggled")) {
            $.cookie("sidebarToggle", 1, { path: '/admin' });
        } else {
            $.cookie("sidebarToggle", 0, { path: '/admin' });
        }
    }
    // For smaller resolutions, the sidebar is collapse with body backdrop
    else {
        $(".sidebar").removeClass("toggled");
        if ($('.modal-backdrop').length) {
            $('.modal-backdrop').fadeOut('slow', function() {
                $(this).remove();
            });
            $(".sidebar").removeClass("show");
            $('body').removeClass('modal-open');
        } else {
            $('<div class="modal-backdrop show z-900"></div>').appendTo('body').hide().fadeIn();
            $(".sidebar").addClass("show");
            $('body').addClass('modal-open');
        }
    }
});
