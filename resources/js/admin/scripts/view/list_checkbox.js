jQuery(document).on('click', '#selectAll, #select-all', function() {
    $('#selectForm .select, #select-form .select').prop('checked', $(this).prop('checked')).trigger('change');
});

jQuery(document).on('change', '#selectForm .select, #select-form .select', function() {
    if ($('#selectForm .select:checked, #select-form .select:checked').length > 0) {
        $('.select-action').fadeIn();
    }
    else {
        $('.select-action').fadeOut();
    }
});
