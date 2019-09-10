@extends('icore::web.layouts.layout')

@section('content')
<div class="jumbotron jumbotron-fluid m-0 background">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <h5 class="card-header">{{ trans('icore::auth.page.login') }}</h5>

                <div class="card-body">
                    @include('icore::web.partials.alerts')

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-lg-4 col-form-label text-lg-right">{{ trans('icore::auth.address') }}</label>

                            <div class="col-lg-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-lg-4 col-form-label text-lg-right">{{ trans('icore::auth.password') }}</label>

                            <div class="col-lg-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-6 offset-lg-4">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="custom-control-label" for="remember">
                                        {{ trans('icore::auth.remember') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-lg-8 offset-lg-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ trans('icore::auth.login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ trans('icore::auth.forgot') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row mb-0">
                            <div class="col-lg-8 offset-lg-4">
                                {{ trans('icore::auth.no_profile') }} <a class="btn btn-outline-primary ml-2"
                                href="{{ route('register') }}">{{ trans('icore::auth.register') }}</a>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row mb-0">
                            <div class="col-lg-8 offset-lg-4">
                                <p>{{ trans('icore::auth.login_with') }}:
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
