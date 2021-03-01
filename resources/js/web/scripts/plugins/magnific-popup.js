$(document).on('readyAndAjax', function () {
    let $lightbox = $('.lightbox');

    if ($lightbox.length) {
        let galleries = $lightbox.map(function () { 
            return $(this).data('gallery'); 
        }).get().filter(function (el, index, arr) {
            return index == arr.indexOf(el);
        });

        $.each(galleries, function (index, value) {
            $('[data-gallery=' + $.escapeSelector(value) + ']').magnificPopup({
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
        });
    }
});
