@extends(config('icore.layout') . '::web.layouts.layout', [
    'title' => [__('Verify Email Address')],
    'desc' => [__('Verify Email Address')],
    'keys' => [__('Verify Email Address')]
])

@section('content')
<div class="jumbotron jumbotron-fluid m-0 background">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <h5 class="card-header">{{ __('Verify Email Address') }}</h5>

                    <div class="card-body">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ trans('icore::auth.success.send_verify') }}
                            </div>
                        @endif

                        {{ __('Before proceeding, please check your email for a verification link.') }}
                        {{ __('If you did not receive the email') }},

                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf

                            <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                                {{ __('click here to request another') }}
                            </button>.
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
