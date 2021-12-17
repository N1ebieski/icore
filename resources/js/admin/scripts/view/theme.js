$(document).on(
    'click.n1ebieski/icore/admin/scripts/view/theme@toggle',
    'div#themeToggle button, div#theme-toggle button',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        if ($element.hasClass('btn-light')) {
            $.cookie("theme_toggle", 'light', { 
                path: '/',
                expires: 365
            });
        }

        if ($element.hasClass('btn-dark')) {
            $.cookie("theme_toggle", 'dark', { 
                path: '/',
                expires: 365
            });
        }

        window.location.reload();
        return;
    }
);
