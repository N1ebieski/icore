<div class="form-group captcha logic_captcha">
    <div style="width:280px;">
        <img src="{{ captcha_base64($id) }}" alt="captcha_base64">
        <input type="hidden" value="{{ $id }}" name="captcha_id">
        <div class="input-group">
            <div class="input-group-prepend">
                <button 
                    class="btn border reload_captcha_base64" 
                    type="button"
                    data-route="{{ route('captcha.base64', ['default']) }}"
                >
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <input class="form-control" type="text" name="captcha">
        </div>
    </div>
    @includeWhen($errors->has('captcha'), 'icore::web.partials.errors', ['name' => 'captcha'])
</div>

@pushonce('script.captcha')
<script src="{{ asset('js/vendor/logic-captcha/captcha_reload.js') }}" defer></script>
@endpushonce
