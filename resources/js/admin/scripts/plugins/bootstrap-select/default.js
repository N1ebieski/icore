jQuery(document).on('readyAndAjax', function() {
    $('.selectpicker').each(function () {
        let $sp = $(this);

        if ($sp.data('loaded') === true) {
            return;
        }

        $sp.selectpicker();

        if ($sp.data('abs') === true) {
            $sp.ajaxSelectPicker({
                ajax: {
                    data: function () {
                        return {
                            filter: {
                                search: '{{{q}}}',
                                status: 1
                            }
                        };
                    }
                },
                preprocessData: function(data) {
                    let array = [];
                    let length = $sp.data('abs-max-options-length') || data.data.length;

                    $.each(data.data, function (key, value) {
                        if (key >= length) {
                            return false;
                        }

                        array.push({
                            value: $sp.data('abs-value-attr') ? value[$sp.data('abs-value-attr')] : value.id,
                            text: $sp.data('abs-text-attr') ? value[$sp.data('abs-text-attr')] : value.name,
                        });
                    });
        
                    return array;
                },
                minLength: 3,
                preserveSelected: typeof $sp.data('abs-preserve-selected') === "boolean" ? $sp.data('abs-preserve-selected') : true,
                preserveSelectedPosition: $sp.data('abs-preserve-selected-position') || 'before',
                langCode: $sp.data('abs-lang-code') || null
            });
        }

        // Fix Cannot unselect option element when preserveSelected is true #85
        $sp.trigger('change').data('AjaxBootstrapSelect').list.cache = {};

        // Fix temporary for jsvalidation errors placement
        $sp.parent().addClass('input-group');

        $sp.attr('data-loaded', true);
    });
});