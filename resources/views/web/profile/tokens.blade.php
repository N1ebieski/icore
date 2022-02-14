@extends(config('icore.layout') . '::web.profile.layouts.layout', [
    'title' => [
        trans('icore::profile.route.tokens'),
        $tokens->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $tokens->currentPage()])
            : null
    ],
    'desc' => [trans('icore::profile.route.tokens')],
    'keys' => [trans('icore::profile.route.tokens')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    {{ trans('icore::profile.route.index') }}
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('icore::profile.route.tokens') }}
</li>
@endsection

@section('content')

@if (session()->has('success'))
<div class="my-3">
    <p>{{ trans('icore::tokens.your_token') }}:</p>
    <div class="clipboard position-relative d-inline pr-3">
        <samp>{{ session()->get('accessToken') }}</samp>
    </div>
</div>
<div class="alert alert-primary" role="alert">
    {{ trans('icore::tokens.copy_info') }}
</div>
@endif

<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fas fa-fw fa-user-lock"></i>
        <span>{{ trans('icore::profile.route.tokens') }}</span>
    </div>
    @can('web.tokens.create')
    @can('api.access')
    <div class="ml-auto text-right responsive-btn-group">
        <button 
            type="button" 
            class="btn btn-primary text-nowrap create" 
            data-toggle="modal"
            data-route="{{ route('web.token.create') }}"
            data-target="#create-modal"
        >    
            <i class="far fa-plus-square"></i>
            <span class="d-none d-sm-inline">
                {{ trans('icore::default.create') }}
            </span>
        </button>
    </div>
    @endcan
    @endcan
</h1>
<div id="filter-content">
    @include('icore::web.profile.partials.token.filter')
    @if ($tokens->isNotEmpty())
    <div id="infinite-scroll">
        @foreach ($tokens as $token)
            @include('icore::web.profile.partials.token.token')
        @endforeach
        @include('icore::web.partials.pagination', [
            'items' => $tokens
        ])
    </div>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>

@component('icore::web.partials.modal')
@slot('modal_id', 'create-modal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('icore::tokens.route.create') }}</span>
@endslot
@endcomponent

@endsection
