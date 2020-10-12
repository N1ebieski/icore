@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [trans('icore::auth.reset')],
    'desc' => [trans('icore::auth.reset')],
    'keys' => [trans('icore::auth.reset')]
])

@section('content')
<div class="jumbotron jumbotron-fluid m-0 background">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <h5 class="card-header">
                        {{ trans('icore::auth.reset') }}
                    </h5>

                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group row">
                                <label 
                                    for="email" 
                                    class="col-md-4 col-form-label text-md-right"
                                >
                                        {{ trans('icore::auth.address.label') }}
                                </label>

                                <div class="col-md-6">
                                    <input 
                                        id="email" 
                                        type="email" 
                                        class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" 
                                        name="email" 
                                        value="{{ $email ?? old('email') }}" 
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
                                    class="col-md-4 col-form-label text-md-right"
                                >
                                    {{ trans('icore::auth.password') }}
                                </label>

                                <div class="col-md-6">
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
                                <label 
                                    for="password-confirm" 
                                    class="col-md-4 col-form-label text-md-right"
                                >
                                    {{ trans('icore::auth.password_confirm') }}
                                </label>

                                <div class="col-md-6">
                                    <input 
                                        id="password-confirm" 
                                        type="password" 
                                        class="form-control" 
                                        name="password_confirmation" 
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ trans('icore::auth.reset') }}
                                    </button>
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
