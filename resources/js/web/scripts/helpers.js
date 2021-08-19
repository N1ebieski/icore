(function($) {
    $.fn.autoHeight = function (options) {
        let options_ = {
            autogrow: typeof options.autogrow === "boolean" ? options.autogrow : true
        };

        function autoHeight_(element) {
            if (element.offsetHeight < element.scrollHeight) {
                return $(element).css({ 'height': 'auto' })
                    .height(element.scrollHeight);
            }

            return $(element);
        }

        if (options_.autogrow === false) {
            return;
        }

        return this.each(function () {
            autoHeight_(this).on('input', function () {
                autoHeight_(this);
            });
        });
    };

    $.fn.removeClassStartingWith = function (begin) {
        this.removeClass(function (index, className) {
            return (className.match(new RegExp("\\b" + begin + "\\S+", "g")) || []).join(' ');
        });
    };

    $.sanitize = function(html) {
        let $output = $($.parseHTML('<div>' + html + '</div>', null, false));

        $output.find('*').each(function(index, node) {
            $.each(node.attributes, function() {
                let attrName = this.name;
                let attrValue = this.value;

                if (attrName.indexOf('on') == 0 || attrValue.indexOf('javascript:') == 0) {
                    $(node).removeAttr(attrName);
                }
            });
        });

        return $output.html();
    };

    $.getUrlParameter = function (url, name) {
        return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
    };

    /**
     * Plugin refreshujący recaptche v2. Potrzebne w przypadku pobrania formularza przez ajax
     */
    $.fn.recaptcha = function () {
        if (this.hasClass('g-recaptcha')) {
            var widgetId;
            // Przypadek, gdy nowy token generowany jest w momencie pobrania formularza
            // przez ajax. Wówczas trzeba go na nowo zrenderować pod nowym widgetId
            if (!this.html().length) {
                widgetId = grecaptcha.render(this[0], {
                    'sitekey' : this.attr('data-sitekey')
                });
            }
            // W przeciwnym razie (tzn. jeśli token jest prawidłowo wygenerowany) pobieramy
            // jego widgetId
            else {
                widgetId = parseInt(this.find('textarea[name="g-recaptcha-response"]').attr('id').match(/\d+$/), 10);
            }

            // Resetowanie tokena. Konieczne w przypadku gdy formularz został wypełniony
            // błędnie, ajax zwrócił errory, bez nowego formularza. W takim przypadku
            // recaptcha nie rozpozna już wcześniejszego rozwiązania, trzeba zresetować i
            // dać użytkownikowi możliwość ponownego przesłania formularza
            if (Number.isInteger(widgetId)) grecaptcha.reset(widgetId);
            else grecaptcha.reset();
        }
    };

    $.fn.captcha = function () {
        if (this.hasClass('logic_captcha')) {
            this.find('input[name="captcha"]').val('');
            this.find('.reload_captcha_base64').trigger('click');
        }
    };

    $.fn.getLoader = function (action, type = 'spinner-border') {
        if (action == 'show') {
            $(this).parent().find('button').prop('disabled', true);
            $(this).find('i').hide();

            let color = $(this).is('[class*="btn-outline-"]') && (typeof $.cookie('theme_toggle') === 'undefined' || $.cookie('theme_toggle') === 'light') ?
                'text-dark' : 'text-light';

            $(this).prepend($.sanitize('<span class="' + type + ' ' + type + '-sm ' + color + '" role="status" aria-hidden="true"></span>'));
        }

        if (action == 'hide') {
            $(this).parent().find('button').prop('disabled', false);
            $(this).find('i').show();
            $(this).find('[role="status"]').remove();
        }
    };

    $.getLoader = function (type = 'spinner-border', loader = 'loader-absolute') {
        return $.sanitize('<div class="' + loader + '"><div class="' + type + '"><span class="sr-only">Loading...</span></div></div>');
    };

    $.getAlert = function (type, text) {
        return $.sanitize('<div class="alert alert-' + type + ' alert-time" role="alert">' + text + '</div>');
    };

    $.getError = function (id, text) {
        return $.sanitize('<span class="invalid-feedback d-block font-weight-bold" id="error-' + id + '">' + text + '</span>');
    };

    $.getMessage = function(text) {
        return $.sanitize('<span class="valid-feedback d-block font-weight-bold">' + text + '</span>');
    };
})(jQuery);
