jQuery(document).on('click', 'div#themeToggle button', function(e) {
    e.preventDefault();

    let $element = $(this);

    if ($element.hasClass('btn-light')) {
        $('link[href*="admin-dark.css"]').attr('href', window.location.origin + '/vendor/icore/css/admin/admin.css');
        $.cookie("themeToggle", 'light', { path: '/' });
    }

    if ($element.hasClass('btn-dark')) {
        $('link[href*="admin.css"]').attr('href', window.location.origin + '/vendor/icore/css/admin/admin-dark.css');
        $.cookie("themeToggle", 'dark', { path: '/' });
    }

    $element.prop('disabled', true);
    $element.siblings('button').prop('disabled', false);
});
