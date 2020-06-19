@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [trans('icore::bans.model.user.route.index'), trans('icore::pagination.page', ['num' => $bans->currentPage()])],
    'desc' => [trans('icore::bans.model.user.route.index')],
    'keys' => [trans('icore::bans.model.user.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.home.index') }}">{{ trans('icore::home.route.index') }}</a></li>
<li class="breadcrumb-item">{{ trans('icore::bans.route.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::bans.model.user.route.index') }}</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fas fa-user-slash"></i></i>
        <span>{{ trans('icore::bans.model.user.route.index') }}</span>
    </div>
</h1>
<div id="filterContent">
    @include('icore::admin.banmodel.user.partials.filter')
    @if ($bans->isNotEmpty())
    <form action="{{ route('admin.banmodel.destroy_global') }}" method="post" id="selectForm">
    @csrf
    @method('delete')
        @can('admin.bans.delete')
        <div class="row my-2">
            <div class="col my-auto">
                <div class="custom-checkbox custom-control">
                    <input type="checkbox" class="custom-control-input" id="selectAll">
                    <label class="custom-control-label" for="selectAll">{{ trans('icore::default.select_all') }}</label>
                </div>
            </div>
        </div>
        @endcan
        <div id="infinite-scroll">
            @foreach ($bans as $ban)
                @include('icore::admin.banmodel.user.partials.ban')
            @endforeach
            @include('icore::admin.partials.pagination', ['items' => $bans])
        </div>
        @can('admin.bans.delete')
        <div class="select-action rounded">
            <button class="btn btn-danger submit" data-toggle="confirmation"
            type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check mr-1"
            data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover" 
            data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
            data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
            data-btn-cancel-icon-class="fas fa-ban mr-1"
            data-title="{{ trans('icore::default.confirm') }}">
                <i class="far fa-trash-alt"></i>
                <span>{{ trans('icore::default.delete_global') }}</span>
            </button>
        </div>
        @endcan
    </form>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>
@endsection
