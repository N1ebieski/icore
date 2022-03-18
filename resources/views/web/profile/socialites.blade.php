@extends(config('icore.layout') . '::web.profile.layouts.layout', [
    'title' => [trans('icore::profile.route.socialites')],
    'desc' => [trans('icore::profile.route.socialites')],
    'keys' => [trans('icore::profile.route.socialites')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    {{ trans('icore::profile.route.index') }}
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('icore::profile.route.socialites') }}
</li>
@endsection

@section('content')
@if (!$user->socialites->isEmpty())
<h1 class="h5 border-bottom pb-2">
    <i class="fas fa-table"></i>
    <span>{{ trans('icore::profile.list_symlink') }}</span>
</h1>
@foreach ($user->socialites as $user_socialite)
<div class="row border-bottom py-3 position-relative transition mb-3">
    <div class="col my-auto d-flex justify-content-between">
        <ul class="list-unstyled my-auto mb-0 pb-0">
            <li>
                <span class="text-primary">
                    <i class="fab fa-{{ $user_socialite->provider_name }}"></i> 
                    <span>{{ ucfirst($user_socialite->provider_name) }}</span>
                </span>
            </li>
        </ul>
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                <form 
                    action="{{ route('web.profile.socialite.destroy', ['socialite' => $user_socialite->id]) }}"
                    method="post"
                >
                    @csrf
                    @method('delete')
                    <button 
                        class="btn btn-danger submit" 
                        data-status="delete" 
                        data-toggle="confirmation"
                        type="button" 
                        data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
                        data-btn-ok-icon-class="fas fa-check mr-1"
                        data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover" 
                        data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                        data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                        data-btn-cancel-icon-class="fas fa-ban"
                        data-title="{{ trans('icore::profile.symlink_confirmation') }}"
                    >
                        <i class="far fa-trash-alt"></i>
                        <span class="d-none d-md-inline">
                            {{ trans('icore::default.delete') }}
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif

@if ($user->socialites->count() < 2)
<h1 class="h5 border-bottom pb-2">
    <i class="fas fa-table"></i>
    <span>{{ trans('icore::profile.list_socialite') }}</span>
</h1>
@foreach (['facebook', 'twitter'] as $provider)
    @if (!$user->socialites->contains('provider_name', $provider))
    <div class="row border-bottom py-3 position-relative transition mb-3">
        <div class="col my-auto d-flex justify-content-between">
            <ul class="list-unstyled my-auto mb-0 pb-0">
                <li>
                    <i class="fab fa-{{ $provider }}"></i> 
                    <span>{{ ucfirst($provider) }}</span>
                </li>
            </ul>
            <div class="text-right ml-3">
                <div class="responsive-btn-group">
                    <a 
                        href="{{ route('web.profile.socialite.redirect', ['provider' => $provider]) }}"
                        role="button" 
                        class="btn btn-primary"
                    >
                        <i class="fas fa-link"></i>
                        <span class="d-none d-md-inline">
                            {{ trans('icore::profile.symlink') }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endif
@endsection
