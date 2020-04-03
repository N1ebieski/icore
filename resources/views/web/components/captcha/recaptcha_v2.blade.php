<div class="form-group">
    <div class="captcha g-recaptcha" data-sitekey="{{ $site_key }}"></div>
    @includeWhen($errors->has('g-recaptcha-response'), 'icore::web.partials.errors', ['name' => 'g-recaptcha-response'])
</div>

@pushonce('script.captcha')
<script defer src="https://www.google.com/recaptcha/api.js"></script>
@endpushonce
