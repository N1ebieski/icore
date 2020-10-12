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
                        @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

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
                                        value="{{ old('email') }}" 
                                        required
                                    >

                                    @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ trans('icore::auth.submit_reset') }}
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
