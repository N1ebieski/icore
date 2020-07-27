jQuery(document).on('click', 'div#themeToggle button', function (e) {
    e.preventDefault();

    let $element = $(this);

    if ($element.hasClass('btn-light')) {
        // $('link[href*="web-dark.css"]').attr('href', function() {
        //     return $(this).attr('href').replace('web-dark.css', 'web.css');
        // });
        $.cookie("themeToggle", 'light', { 
            path: '/',
            expires: 365
        });
    }

    if ($element.hasClass('btn-dark')) {
        // $('link[href*="web.css"]').attr('href', function() {
        //     return $(this).attr('href').replace('web.css', 'web-dark.css');
        // });
        $.cookie("themeToggle", 'dark', { 
            path: '/',
            expires: 365
        });
    }

    window.location.reload();
    // $element.prop('disabled', true);
    // $element.siblings('button').prop('disabled', false);
});
