(function($) {
    $.fn.serializeObject = function() {

        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
                "push":     /^$/,
                "fixed":    /^\d+$/,
                "named":    /^[a-zA-Z0-9_]+$/
            };


        this.build = function(base, key, value){
            base[key] = value;
            return base;
        };

        this.push_counter = function(key){
            if(push_counters[key] === undefined){
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };

        $.each($(this).serializeArray(), function(){

            // Skip invalid keys
            if(!patterns.validate.test(this.name)){
                return;
            }

            var k,
                keys = this.name.match(patterns.key),
                merge = this.value,
                reverse_key = this.name;

            while((k = keys.pop()) !== undefined){

                // Adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // Push
                if(k.match(patterns.push)){
                    merge = self.build([], self.push_counter(reverse_key), merge);
                }

                // Fixed
                else if(k.match(patterns.fixed)){
                    merge = self.build([], k, merge);
                }

                // Named
                else if(k.match(patterns.named)){
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
        });

        return json;
    };

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
     * Plugin refreshujący recaptche v2/invisible. Potrzebne w przypadku pobrania formularza przez ajax
     */
    $.fn.recaptcha = function () {
        if (this.hasClass('g-recaptcha')) {
            let callback = null;
            let $button;

            // W przypadku recaptcha invisible musimy pobrać button i zdefiniować callback
            if (this.data('size') === 'invisible') {
                let $form = this.closest('form');
                
                if ($form.is('[id]')) {
                    $button = $(`button[form="${$form.attr('id')}"]:last`);
                }

                if (typeof $button === 'undefined' || !$button.length) {
                    $button = $form.find('button[class*="btn-primary"]:last, button[type="submit"]:last');
                }

                callback = function () {
                    if ($button.attr('type') === 'submit') {
                        $button.closest('form').submit();
                    } else {
                        $button.trigger('click');
                    }
                }

                $button.addClass('captcha');
            }

            let widgetId;
            // Przypadek, gdy nowy token generowany jest w momencie pobrania formularza
            // przez ajax. Wówczas trzeba go na nowo zrenderować pod nowym widgetId
            if (!this.html().length) {
                widgetId = grecaptcha.render(this[0], {
                    'sitekey': this.attr('data-sitekey'),
                    'callback': callback
                });              
            }
            // W przeciwnym razie (tzn. jeśli token jest prawidłowo wygenerowany) pobieramy
            // jego widgetId
            else {
                widgetId = parseInt(this.find('textarea[name="g-recaptcha-response"]').attr('id').match(/\d+$/), 10);
            }

            if (typeof $button !== 'undefined' && $button.length) {
                $button.attr('data-widgetid', widgetId);
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

    $.fn.loader = function (action, type = 'spinner-border') {
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

    $.fn.addLoader = function (options = null) {
        options = {
            type: typeof options === 'string' ? options : 'spinner-border',
            class: options?.class || 'loader-absolute'
        };

        return this.append($.sanitize(`
            <div class="${options.class}">
                <div class="${options.type}">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `));
    };

    $.fn.addAlert = function (options) {
        options = {
            message: typeof options === 'string' ? options : options.message,
            type: options.type || 'danger'
        };

        return this.prepend($.sanitize(`
            <div class="alert alert-${options.type} alert-time" role="alert">
                <button 
                    type="button" 
                    class="text-dark close" 
                    data-dismiss="alert" 
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
                ${options.message}
            </div>
        `));
    };
        
    $.fn.addToast = function (options) {
        options = {
            title: typeof options === 'string' ? options : options.title,
            type: options.type || 'success',
            message: options.message || ''
        };

        let $toast = $($.sanitize(`
            <div>
                <div 
                    class="toast bg-${options.type}"
                    role="alert" 
                    aria-live="assertive" 
                    aria-atomic="true" 
                    data-delay="20000" 
                >
                    <div class="toast-header">
                        <strong class="mr-auto">${options.title}</strong>
                        <button 
                            type="button" 
                            class="text-dark ml-2 mb-1 close" 
                            data-dismiss="toast" 
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>             
                </div>
            </div>
        `));

        if (options.message.length) {
            $toast.find('.toast').append($.sanitize(`
                <div class="toast-body bg-light text-dark">
                    ${options.message}
                </div>
            `));
        }

        return this.append($toast.html());
    };

    $.fn.addError = function (options) {
        options = {
            message: typeof options === 'string' ? options : options.message,
            id: options.id || null
        };

        let $error = $($.sanitize(`
            <div>
                <span class="invalid-feedback d-block font-weight-bold">${options.message}</span>
            </div>
        `));

        if (options.id !== null) {
            $error.find('.invalid-feedback').attr('id', options.id);
        }

        return this.append($error.html());
    };

    $.fn.addMessage = function (options) {
        options = {
            message: typeof options === 'string' ? options : options.message,
            id: options.id || null
        };

        let $message = $($.sanitize(`
            <div>
            <span class="valid-feedback d-block font-weight-bold">${options.message}</span>
            </div>
        `));

        if (options.id !== null) {
            $message.find('.valid-feedback').attr('id', options.id);
        }

        return this.append($message.html());
    };
})(jQuery);
