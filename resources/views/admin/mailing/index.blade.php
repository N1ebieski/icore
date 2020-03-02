@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [trans('icore::mailings.page.index'), trans('icore::pagination.page', ['num' => $mailings->currentPage()])],
    'desc' => [trans('icore::mailings.page.index')],
    'keys' => [trans('icore::mailings.page.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.home.index') }}">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::mailings.page.index') }}</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fas fa-fw fa-envelope"></i>&nbsp;{{ trans('icore::mailings.page.index') }}
    </div>
    @can('create mailings')
    <div class="ml-auto text-right">
        <a href="{{ route('admin.mailing.create') }}" role="button" class="btn btn-primary text-nowrap">
            <i class="far fa-plus-square"></i>
            <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::mailings.create') }}</span>
        </a>
    </div>
    @endcan
</h1>
<div id="filterContent">
    @include('icore::admin.mailing.partials.filter')
    @if ($mailings->isNotEmpty())
    <form action="{{ route('admin.mailing.destroy_global') }}" method="post" id="selectForm">
    @csrf
    @method('delete')
        @can('destroy mailings')
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
            @foreach ($mailings as $mailing)
                @include('icore::admin.mailing.partials.mailing')
            @endforeach
            @include('icore::admin.partials.pagination', ['items' => $mailings])
        </div>
        @can('destroy mailings')
        <div class="select-action rounded">
            <button class="btn btn-danger submit" data-toggle="confirmation"
            type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
            data-btn-ok-icon-class="fas fa-check mr-1"
            data-btn-ok-class="btn h-100 d-flex align-items-center btn-primary btn-popover" 
            data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
            data-btn-cancel-class="btn h-100 d-flex align-items-center btn-secondary btn-popover" 
            data-btn-cancel-icon-class="fas fa-ban mr-1"
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
