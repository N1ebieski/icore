jQuery(document).on('readyAndAjax', function () {
    $('.lazy').lazy({
        effect: "fadeIn",
        effectTime: "fast",
        threshold: 0
    });
});