@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [trans('icore::auth.route.login')],
    'desc' => [trans('icore::auth.route.login')],
    'keys' => [trans('icore::auth.route.login')]
])

@section('content')
<div class="jumbotron jumbotron-fluid m-0 background">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <h5 class="card-header">
                        {{ trans('icore::auth.route.login') }}
                    </h5>

                    <div class="card-body">
                        @include('icore::web.partials.alerts')

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group row">
                                <label 
                                    for="email" 
                                    class="col-lg-4 col-form-label text-lg-right"
                                >
                                    {{ trans('icore::auth.address.label') }}
                                </label>

                                <div class="col-lg-6">
                                    <input 
                                        id="email" 
                                        type="email" 
                                        class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required 
                                        autofocus
                                    >

                                    @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
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
                                        class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" 
                                        name="password" 
                                        required
                                    >

                                    @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-6 offset-lg-4">
                                    <div class="custom-control custom-switch">
                                        <input 
                                            class="custom-control-input" 
                                            type="checkbox" 
                                            name="remember" 
                                            id="remember" {{ old('remember') ? 'checked' : '' }}
                                        >

                                        <label class="custom-control-label" for="remember">
                                            {{ trans('icore::auth.remember') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-lg-8 offset-lg-4">
                                    <input 
                                        type="hidden" 
                                        name="redirect" 
                                        value="{{ old('redirect', url()->previous() ?? null) }}"
                                    >

                                    <button type="submit" class="btn btn-primary">
                                        {{ trans('icore::auth.login') }}
                                    </button>

                                    @if (app('router')->has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ trans('icore::auth.reset') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            @if (app('router')->has('register'))
                            <div class="form-group row mb-0">
                                <div class="col-lg-8 offset-lg-4">
                                    <span class="mr-1">
                                        {{ trans('icore::auth.no_profile') }}
                                    </span>
                                    <a 
                                        class="btn btn-outline-primary" 
                                        href="{{ route('register') }}"
                                        title="{{ trans('icore::auth.register') }}"
                                    >
                                        {{ trans('icore::auth.register') }}
                                    </a>
                                </div>
                            </div>
                            @if (app('router')->has('auth.socialite.redirect'))
                            <hr>
                            <div class="form-group row mb-0">
                                <div class="col-lg-8 offset-lg-4">
                                    <div class="d-flex">
                                        <div class="mr-2 text-nowrap">
                                            {{ trans('icore::auth.login_with') }}:
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
                                                href="{{ route('auth.socialite.redirect', ['provider' => 'twitter-oauth-2']) }}" 
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
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
