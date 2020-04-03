<script>
function jsvalidation() {
    
    $("<?= $validator['selector']; ?>").each(function() {
        jQuery_2_1_3(this).validate({
            errorElement: 'span',
            errorClass: 'invalid-feedback font-weight-bold',

            errorPlacement: function (error, element) {
                let selector = jQuery.escapeSelector(element.attr('name').replace(/\[/g, '.').replace(/\]/g, '').replace(/\.$/, ''));
                $('#error-' + selector).remove();
                if (element.parent('.input-group').length ||
                    element.prop('type') === 'checkbox' || element.prop('type') === 'radio' || element.prop('type') === 'file') {
                    error.insertAfter(element.closest('#' + selector));
                    // else just place the validation message immediately after the input
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid'); // add the Bootstrap error class to the control group
            },

            <?php if (isset($validator['ignore']) && is_string($validator['ignore'])): ?>

            ignore: "<?= $validator['ignore']; ?>",
            <?php endif; ?>


            unhighlight: function(element) {
                var fields = <?= json_encode(array_keys($validator['rules'])); ?>;

                if ($.inArray($(element).attr('name').replace('[]', ''), fields) != -1) {
                    $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
                }
            },

            success: function (element) {
                $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid'); // remove the Boostrap error class from the control group
            },

            focusInvalid: false, // do not focus the last invalid input
            <?php if (Config::get('jsvalidation.focus_on_error')): ?>
            invalidHandler: function (form, validator) {

                if (!validator.numberOfInvalids())
                    return;

                $('html, body').animate({
                    scrollTop: $(validator.errorList[0].element).offset().top - 100
                }, <?= Config::get('jsvalidation.duration_animate') ?>);
                $(validator.errorList[0].element).focus();

            },
            <?php endif; ?>

            rules: <?= json_encode($validator['rules']); ?>
        });
    });
}

jQuery_2_1_3(document).ready(jsvalidation);
jQuery_2_1_3(document).ajaxComplete(jsvalidation);
</script>
