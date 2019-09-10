jQuery(document).ready(function() {
    $('form#createPost .datepicker, form#editFullPost .datepicker').pickadate({
        clear: '',
        formatSubmit: 'yyyy-m-dd',
        hiddenName: true
    });
    $('form#createMailing .datepicker, form#editMailing .datepicker').pickadate({
        clear: '',
        formatSubmit: 'yyyy-m-dd',
        hiddenName: true,
        min: new Date(),
    });
    $('.timepicker').pickatime({
        clear: '',
        format: 'H:i',
        formatSubmit: 'HH:i',
        hiddenName: true
    });
});
