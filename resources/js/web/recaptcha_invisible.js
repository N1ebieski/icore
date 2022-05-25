$(document).on('ready.n1ebieski/icore/web/recaptcha_invisible@init', function () {
    $('.g-recaptcha[data-size="invisible"]').each(function (i, e) {
        grecaptcha.ready(function () {
            $(e).recaptcha();
        });
    });
});

$(document).on('click.n1ebieski/icore/web/recaptcha_invisible@captcha', 'button', function (e) {
    if ($(this).hasClass('captcha')) {
        e.preventDefault();
        e.stopImmediatePropagation();

        let $form;

        if ($(this).is('[form]')) {
            $form = $(`form#${$(this).attr('form')}`);
        }
        
        if (typeof $form === 'undefined' || !$form.length) {
            $form = $(this).closest('form');
        }

        let valid = $.prototype.valid ? $form.valid() : true;

        if (valid) {
            $(this).removeClass('captcha');

            grecaptcha.execute($(this).data('widgetid'));    
        }

        return;
    }
});
