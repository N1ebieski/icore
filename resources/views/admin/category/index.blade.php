@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [trans('icore::categories.route.index'), trans('icore::pagination.page', ['num' => $categories->currentPage()])],
    'desc' => [trans('icore::categories.route.index')],
    'keys' => [trans('icore::categories.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.home.index') }}">{{ trans('icore::home.route.index') }}</a></li>
<li class="breadcrumb-item">{{ trans('icore::categories.route.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">{{ trans("icore::categories.{$model->poli}.{$model->poli}") }}</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fas fa-fw fa-layer-group"></i>
        <span>{{ trans('icore::categories.route.index') }}</span>
    </div>
    @can('create categories')
    <div class="ml-auto text-right">
        <button type="button" class="btn btn-primary text-nowrap create" data-toggle="modal"
        data-route="{{ route("admin.category.{$model->poli}.create") }}" data-target="#createModal">
            <i class="far fa-plus-square"></i>
            <span class="d-none d-sm-inline">{{ trans('icore::categories.create') }}</span>
        </button>
    </div>
    @endcan
</h1>
<div id="filterContent">
    @include('icore::admin.category.partials.filter')
    @if ($categories->isNotEmpty())
    <form action="{{ route('admin.category.destroy_global') }}" method="post" id="selectForm">
    @csrf
    @method('delete')
        @can('destroy categories')
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
            @foreach ($categories as $category)
                @include('icore::admin.category.partials.category', ['category' => $category])
            @endforeach
            @include('icore::admin.partials.pagination', ['items' => $categories])
        </div>
        @can('destroy categories')
        <div class="select-action rounded">
            <button class="btn btn-danger submit" data-toggle="confirmation"
            type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check mr-1"
            data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover" 
            data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
            data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
            data-btn-cancel-icon-class="fas fa-ban mr-1"
            data-title="{{ trans('icore::categories.confirm') }}">
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

@component('icore::admin.partials.modal')
@slot('modal_id', 'editModal')
@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::categories.route.edit') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'editPositionModal')
@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('icore::categories.route.edit_position') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'createModal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('icore::categories.route.create') }}</span>
@endslot
@endcomponent

@endsection
