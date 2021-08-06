<h5 class="mt-3 mb-4">
    {{ trans('icore::newsletter.subscribe') }}
</h5>
<div class="mb-3">
    <form data-route="{{ route('web.newsletter.store') }}">
        <div class="form-group input-group m-0 p-0">
            <input 
                type="text" 
                name="email" 
                class="form-control" 
                placeholder="{{ trans('icore::newsletter.email.placeholder') }}"
                aria-label="{{ trans('icore::newsletter.email.placeholder') }}"
            >
            <div class="input-group-append">
                <button class="btn btn-primary store-newsletter" type="button">
                    {{ trans('icore::default.save') }}
                </button>
            </div>
        </div>
        <div class="form-group mt-2">
            <div class="custom-control custom-checkbox">
                <input 
                    type="checkbox" 
                    class="custom-control-input" 
                    id="marketing_agreement" 
                    name="marketing_agreement" 
                    value="1"
                >
                <label class="custom-control-label text-left" for="marketing_agreement">
                    <small>{{ trans('icore::policy.agreement.marketing') }}</small>
                </label>
            </div>
        </div>
    </form>
</div>
