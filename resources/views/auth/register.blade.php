@extends('icore::web.layouts.layout')

@section('content')
<div class="jumbotron jumbotron-fluid m-0 background">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <h5 class="card-header">{{ trans('icore::auth.register') }}</h5>

                    <div class="card-body">
                        @include('icore::web.partials.alerts')

                        <form method="POST" action="{{ route('register') }}" id="register">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-lg-4 col-form-label text-lg-right">{{ trans('icore::auth.name') }}</label>

                                <div class="col-lg-6">
                                    <input id="name" type="text" class="form-control @isValid('name')" name="name" value="{{ old('name') }}" required autofocus>

                                    @includeWhen($errors->has('name'), 'icore::web.partials.errors', ['name' => 'name'])
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-lg-right">{{ trans('icore::auth.address') }}</label>

                                <div class="col-lg-6">
                                    <input id="email" type="email" class="form-control @isValid('email')" name="email" value="{{ old('email') }}" required>

                                    @includeWhen($errors->has('email'), 'icore::web.partials.errors', ['name' => 'email'])
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-lg-4 col-form-label text-lg-right">{{ trans('icore::auth.password') }}</label>

                                <div class="col-lg-6">
                                    <input id="password" type="password" class="form-control @isValid('password')" name="password" required>

                                    @includeWhen($errors->has('password'), 'icore::web.partials.errors', ['name' => 'password'])
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-lg-4 col-form-label text-lg-right">{{ trans('icore::auth.password_confirm') }}</label>

                                <div class="col-lg-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
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
                            <hr>
                            <div class="form-group row mb-0">
                                <div class="col-lg-8 offset-lg-4">
                                    <p>{{ trans('icore::auth.register_with') }}:
                                        <a href="{{ route('auth.socialite.redirect', ['provider' => 'facebook']) }}" class="ml-2"><i class="fab fa-facebook"></i> Facebook</a>
                                        <a href="{{ route('auth.socialite.redirect', ['provider' => 'twitter']) }}" class="ml-2"><i class="fab fa-twitter"></i> Twitter</a>
                                    </p>
                                </div>
                            </div>
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
