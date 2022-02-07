<div class="form-group">
    <div class="captcha g-recaptcha" data-sitekey="{{ $site_key }}" data-size="invisible"></div>
    @includeWhen($errors->has('g-recaptcha-response'), 'icore::web.partials.errors', ['name' => 'g-recaptcha-response'])
</div>

@pushonce('script.captcha')
<script src="https://www.google.com/recaptcha/api.js?render=explicit" defer></script>
<script src="{{ asset('js/vendor/icore/web/recaptcha_invisible.js') }}" defer></script>
@endpushonce
