jQuery(document).on('click', '#selectAll', function() {
    $('#selectForm .select').prop('checked', $(this).prop('checked')).trigger('change');
});

jQuery(document).on('change', '#selectForm .select', function() {
    if ($('#selectForm .select:checked').length > 0) {
        $('.select-action').fadeIn();
    }
    else {
        $('.select-action').fadeOut();
    }
});
