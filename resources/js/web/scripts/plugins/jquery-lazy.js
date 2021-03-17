jQuery(document).on('readyAndAjax', function () {
    $('.lazy').lazy({
        effect: "fadeIn",
        effectTime: "fast",
        threshold: 0,
        placeholder: 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='
    });
});