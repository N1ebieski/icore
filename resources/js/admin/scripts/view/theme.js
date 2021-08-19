jQuery(document).on('click', 'div#themeToggle button, div#theme-toggle button', function (e) {
    e.preventDefault();

    let $element = $(this);

    if ($element.hasClass('btn-light')) {
        // $('link[href*="admin-dark.css"]').attr('href', function() {
        //     return $(this).attr('href').replace('admin-dark.css', 'admin.css');
        // });
        $.cookie("theme_toggle", 'light', { 
            path: '/',
            expires: 365
        });
    }

    if ($element.hasClass('btn-dark')) {
        // $('link[href*="admin.css"]').attr('href', function() {
        //     return $(this).attr('href').replace('admin.css', 'admin-dark.css');
        // });
        $.cookie("theme_toggle", 'dark', { 
            path: '/',
            expires: 365
        });
    }

    window.location.reload();
    // $element.prop('disabled', true);
    // $element.siblings('button').prop('disabled', false);
});
