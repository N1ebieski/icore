jQuery(document).on('readyAndAjax', function() {
    $('.selectpicker').each(function () {
        if ($(this).data('loaded') === true) {
            return;
        }

        $(this).selectpicker();

        if ($(this).data('abs') === true) {
            $(this).ajaxSelectPicker({
                ajax: {
                    data: function () {
                        return {
                            filter: {
                                search: '{{{q}}}'
                            }
                        };
                    }
                },
                preprocessData: function(data) {
                    let array = [];
                    let length = $(this).data('abs-max-options-length') || data.data.length;

                    $.each(data.data, function (key, value) {
                        if (key >= length) {
                            return false;
                        }

                        array.push({
                            value: $(this).data('abs-value-attr') ? value[$(this).data('abs-value-attr')] : value.id,
                            text: $(this).data('abs-text-attr') ? value[$(this).data('abs-text-attr')] : value.name,
                        });
                    });
        
                    return array;
                },
                minLength: 3,
                preserveSelected: typeof $(this).data('abs-preserve-selected') === "boolean" ? $(this).data('abs-preserve-selected') : true,
                preserveSelectedPosition: $(this).data('abs-preserve-selected-position') || 'before',
                langCode: $(this).data('abs-lang-code') || null
            });
        }

        $(this).attr('data-loaded', true);
    });
});