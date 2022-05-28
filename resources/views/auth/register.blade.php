@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [trans('icore::auth.register')],
    'desc' => [trans('icore::auth.register')],
    'keys' => [trans('icore::auth.register')]
])

@section('content')
<div class="jumbotron jumbotron-fluid m-0 background">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <h5 class="card-header">
                        {{ trans('icore::auth.register') }}
                    </h5>

                    <div class="card-body">
                        @include('icore::web.partials.alerts')

                        <form method="POST" action="{{ route('register') }}" id="register">
                            @csrf

                            <div class="form-group row">
                                <label 
                                    for="name" 
                                    class="col-lg-4 col-form-label text-lg-right"
                                >
                                    {{ trans('icore::auth.name.label') }}
                                </label>

                                <div class="col-lg-6">
                                    <input 
                                        id="name" 
                                        type="text" 
                                        class="form-control {{ $isValid('name') }}" 
                                        name="name" 
                                        value="{{ old('name') }}" 
                                        required 
                                        autofocus
                                    >

                                    @includeWhen($errors->has('name'), 'icore::web.partials.errors', ['name' => 'name'])
                                </div>
                            </div>

                            <div class="form-group row">
                                <label 
                                    for="email" 
                                    class="col-md-4 col-form-label text-lg-right"
                                >
                                    {{ trans('icore::auth.address.label') }}
                                </label>

                                <div class="col-lg-6">
                                    <input 
                                        id="email" 
                                        type="email" 
                                        class="form-control {{ $isValid('email') }}" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required
                                    >

                                    @includeWhen($errors->has('email'), 'icore::web.partials.errors', ['name' => 'email'])
                                </div>
                            </div>

                            <div class="form-group row">
                                <label 
                                    for="password" 
                                    class="col-lg-4 col-form-label text-lg-right"
                                >
                                    {{ trans('icore::auth.password') }}
                                </label>

                                <div class="col-lg-6">
                                    <input 
                                        id="password" 
                                        type="password" 
                                        class="form-control {{ $isValid('password') }}" 
                                        name="password" 
                                        required
                                    >

                                    @includeWhen($errors->has('password'), 'icore::web.partials.errors', ['name' => 'password'])
                                </div>
                            </div>

                            <div class="form-group row">
                                <label 
                                    for="password-confirm" 
                                    class="col-lg-4 col-form-label text-lg-right"
                                >
                                    {{ trans('icore::auth.password_confirm') }}
                                </label>

                                <div class="col-lg-6">
                                    <input 
                                        id="password-confirm" 
                                        type="password" 
                                        class="form-control" 
                                        name="password_confirmation" 
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label text-lg-right d-none d-lg-block"></label>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input 
                                                type="checkbox" 
                                                class="custom-control-input {{ $isValid('privacy_agreement') }}" 
                                                id="privacy_agreement" 
                                                name="privacy_agreement" 
                                                value="1"
                                                {{ old('privacy_agreement') == true ? 'checked' : null }}
                                            >
                                            <label 
                                                class="custom-control-label text-left" 
                                                for="privacy_agreement"
                                            >
                                                <small>
                                                    {!! trans('icore::policy.agreement.privacy', [
                                                        'privacy' => route('web.page.show', [str_slug(trans('icore::policy.privacy'))])
                                                    ]) !!}
                                                </small>
                                            </label>
                                        </div>
                                        @includeWhen($errors->has('privacy_agreement'), 'icore::web.partials.errors', ['name' => 'privacy_agreement'])
                                    </div>
                                </div>

                                <label class="col-lg-4 col-form-label text-lg-right d-none d-lg-block"></label>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input 
                                                type="checkbox" 
                                                class="custom-control-input {{ $isValid('contact_agreement') }}" 
                                                id="contact_agreement" 
                                                name="contact_agreement" 
                                                value="1"
                                                {{ old('contact_agreement') == true ? 'checked' : null }}
                                            >
                                            <label 
                                                class="custom-control-label text-left" 
                                                for="contact_agreement"
                                            >
                                                <small>{{ trans('icore::policy.agreement.register') }}</small>
                                            </label>
                                        </div>
                                        @includeWhen($errors->has('contact_agreement'), 'icore::web.partials.errors', ['name' => 'contact_agreement'])
                                    </div>
                                </div>

                                <label class="col-lg-4 col-form-label text-lg-right d-none d-lg-block"></label>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input 
                                                type="checkbox" 
                                                class="custom-control-input {{ $isValid('marketing_agreement') }}" 
                                                id="marketing_agreement" 
                                                name="marketing_agreement" 
                                                value="1"
                                                {{ old('marketing_agreement') == true ? 'checked' : null }}
                                            >
                                            <label 
                                                class="custom-control-label text-left" 
                                                for="marketing_agreement"
                                            >
                                                <small>{{ trans('icore::policy.agreement.marketing') }}</small>
                                            </label>
                                        </div>
                                        @includeWhen($errors->has('marketing_agreement'), 'icore::web.partials.errors', ['name' => 'marketing_agreement'])
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-lg-6 offset-lg-4">
                                    @render('icore::captchaComponent')
                                    <button type="submit" class="btn btn-primary">
                                        {{ trans('icore::auth.register') }}
                                    </button>
                                </div>
                            </div>

                            @if (app('router')->has('auth.socialite.redirect'))
                            <hr>
                            <div class="form-group row mb-0">
                                <div class="col-lg-8 offset-lg-4">
                                    <div class="d-flex">
                                        <div class="mr-2 text-nowrap">
                                            {{ trans('icore::auth.register_with') }}:
                                        </div>
                                        <div>
                                            <a 
                                                href="{{ route('auth.socialite.redirect', ['provider' => 'facebook']) }}" 
                                                class="mr-2 text-nowrap" 
                                                title="Facebook"
                                            >
                                                <i class="fab fa-facebook"></i>
                                                <span> Facebook</span>
                                            </a>
                                            <a 
                                                href="{{ route('auth.socialite.redirect', ['provider' => 'twitter']) }}" 
                                                class="mr-2 text-nowrap" 
                                                title="Twitter"
                                            >
                                                <i class="fab fa-twitter"></i> 
                                                <span> Twitter</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
@component('icore::web.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Auth\Register\StoreRequest', '#register'); !!}
@endcomponent
@endpush
