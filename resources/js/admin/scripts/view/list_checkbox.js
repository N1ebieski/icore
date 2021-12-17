$(document).on(
    'click.n1ebieski/icore/admin/scripts/view/list_checkbox@selectAll',
    '#selectAll, #select-all',
    function () {
        $('#selectForm .select, #select-form .select').prop('checked', $(this).prop('checked')).trigger('change');
    }
);

$(document).on(
    'change.n1ebieski/icore/admin/scripts/view/list_checkbox@select',
    '#selectForm .select, #select-form .select',
    function () {
        if ($('#selectForm .select:checked, #select-form .select:checked').length > 0) {
            $('.select-action').fadeIn();
        }
        else {
            $('.select-action').fadeOut();
        }
    }
);
