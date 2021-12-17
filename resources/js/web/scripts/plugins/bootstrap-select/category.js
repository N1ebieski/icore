$(document).on('readyAndAjax.n1ebieski/icore/web/scripts/plugins/bootstrap-select/category@init', function () {
    $('.select-picker-category').each(function () {
        let $sp = $(this);

        if ($sp.data('loaded') === true) {
            return;
        }

        $sp.selectpicker().on('changed.bs.select', function () {
            $sp.next('button').find('.filter-option-inner-inner > small').remove();
        }).on('shown.bs.select', function () {
            $sp.parent().find('.dropdown-menu').find('input[type="search"]').attr('name', 'search');
        }).trigger('change');

        if ($sp.data('abs') === true) {
            $sp.ajaxSelectPicker({
                ajax: {
                    data: function () {
                        return {
                            filter: {
                                search: '{{{q}}}',
                                orderby: 'real_depth|desc',
                                except: $sp.data('abs-filter-except') || null,
                                status: 1
                            }
                        };
                    }
                },
                preprocessData: function(data) {
                    let array = [];
                    let length = $sp.data('abs-max-options-length') || data.data.length;

                    let defaultOptions = $sp.data('abs-default-options') || [];

                    $.each(defaultOptions, function (key, value) {
                        array.push({
                            value: value.value,
                            text: value.text
                        });
                    });

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

            // Fix Cannot unselect option element when preserveSelected is true #85
            $sp.trigger('change').data('AjaxBootstrapSelect').list.cache = {};
        }

        // Fix temporary for jsvalidation errors placement
        $sp.parent().addClass('input-group');

        $sp.attr('data-loaded', true);
    });
});
