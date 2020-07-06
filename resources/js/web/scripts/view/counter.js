jQuery(document).on('readyAndAjax', function () {
    $('.counter').each(function () {
        let $counter = $(this);
        $counter.name = $.escapeSelector($counter.data('name'));
        $counter.min = typeof $counter.data('min') !== 'undefined' && Number.isInteger($counter.data('min')) ?
            $counter.data('min') : null;
        $counter.max = typeof $counter.data('max') !== 'undefined' && Number.isInteger($counter.data('max')) ?
            $counter.data('max') : null;

        let counter = function () {
            let $elements = [
                $('[name="' + $counter.name + '"]'),
                $('[name="' + $counter.name + '"]').hasClass('trumbowyg-textarea') ?
                    $('[name="' + $counter.name + '"]').parent().find('.trumbowyg-editor')
                    : null
            ];
    
            $.each($elements.filter((item) => item != null), function () {
                $(this).keyup(function () {
                    let length = $(this).attr('contenteditable') ?
                        parseFloat($(this).text().length)
                        : parseFloat($($.parseHTML($(this).val())).text().length);

                    $counter.firstchild = $counter.children(":first");
        
                    $counter.firstchild.text(length);

                    if (length === 0) {
                        $counter.firstchild.removeClass();
                    } else {          
                        $counter.firstchild.addClass('text-success');
                        $counter.firstchild.removeClass('text-danger');

                        if (($counter.min !== null && length < $counter.min) || ($counter.max !== null && length > $counter.max)) {
                            $counter.firstchild.addClass('text-danger');
                            $counter.firstchild.removeClass('text-success');
                        }
                    }
                });
            });
        };

        if ($('[name="' + $counter.name + '"]').attr('id').indexOf('trumbowyg') !== -1) {
            $('#' + $('[name="' + $counter.name + '"]').attr('id')).on('tbwinit', () => counter());
        } else {
            counter();
        }
    });
});