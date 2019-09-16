@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [trans('icore::bans.model.user.page.index'), trans('icore::pagination.page', ['num' => $bans->currentPage()])],
    'desc' => [trans('icore::bans.model.user.page.index')],
    'keys' => [trans('icore::bans.model.user.page.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.home.index') }}">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item">{{ trans('icore::bans.page.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::bans.model.user.page.index') }}</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fas fa-user-slash"></i></i>&nbsp;{{ trans('icore::bans.model.user.page.index') }}
    </div>
</h1>
<div id="filterContent">
    @include('icore::admin.banmodel.user.filter')
    @if ($bans->isNotEmpty())
    <form action="{{ route('admin.banmodel.destroy_global') }}" method="post" id="selectForm">
    @csrf
    @method('delete')
        @can('delete bans')
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
                @include('icore::admin.banmodel.user.ban')
            @endforeach
            @include('icore::admin.partials.pagination', ['items' => $bans])
        </div>
        @can('delete bans')
        <div class="select-action rounded">
            <button class="btn btn-danger submit" data-toggle="confirmation"
            type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check"
            data-btn-ok-class="btn-primary btn-popover" data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
            data-btn-cancel-class="btn-secondary btn-popover" data-btn-cancel-icon-class="fas fa-ban"
            data-title="{{ trans('icore::default.confirm') }}">
                <i class="far fa-trash-alt"></i>&nbsp;{{ trans('icore::default.delete_global') }}
            </button>
        </div>
        @endcan
    </form>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>
@endsection
