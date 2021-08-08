jQuery(document).on('readyAndAjax', function() {
    $('.selectpicker-category').each(function () {
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
                                orderby: 'real_depth|desc',
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
                            data: {
                                content: value.ancestors.length ?
                                    '<small class="p-0 m-0">' + value.ancestors.map(item => item.name).join(' &raquo; ') + ' &raquo; </small>' + value.name
                                    : null
                            }
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

        // Fix Remove data content from select form
        //$sp.next('button').find('.filter-option-inner-inner > small').remove();

        // Fix Cannot unselect option element when preserveSelected is true #85
        $sp.trigger('change').data('AjaxBootstrapSelect').list.cache = {};

        // Fix temporary for jsvalidation errors placement
        $sp.parent().addClass('input-group');

        $sp.attr('data-loaded', true);
    });
});
