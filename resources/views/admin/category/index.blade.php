@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [
        trans('icore::categories.route.index'),
        $categories->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $categories->currentPage()])
            : null
    ],
    'desc' => [trans('icore::categories.route.index')],
    'keys' => [trans('icore::categories.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    {{ trans('icore::categories.route.index') }}
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans("icore::categories.{$model->poli}.{$model->poli}") }}
</li>
@endsection

@section('content')
<div id="filter-content">
    <h1 class="h5 border-bottom pb-2 d-flex">
        <div class="mr-auto my-auto">
            <i class="fas fa-fw fa-layer-group"></i>
            <span>{{ trans('icore::categories.route.index') }}</span>
        </div>
        @can('admin.categories.create')
        <div class="ml-auto text-right responsive-btn-group">
            <button 
                type="button" 
                class="btn btn-primary text-nowrap create" 
                data-toggle="modal"
                data-route="{{ route("admin.category.{$model->poli}.create", ['parent_id' => $filter['parent'] ?? null]) }}"
                data-target="#create-modal"
            >
                <i class="far fa-plus-square"></i>
                <span class="d-none d-sm-inline">
                    {{ trans('icore::default.create') }}
                </span>
            </button>
        </div>
        @endcan
    </h1>
    @include('icore::admin.category.partials.filter')
    @if ($categories->isNotEmpty())
    <form 
        action="{{ route('admin.category.destroy_global') }}" 
        method="post" 
        id="select-form"
    >
        @csrf
        @method('delete')
        @can('admin.categories.delete')
        <div class="row my-2">
            <div class="col my-auto">
                <div class="custom-checkbox custom-control">
                    <input 
                        type="checkbox" 
                        class="custom-control-input" 
                        id="select-all"
                    >
                    <label class="custom-control-label" for="select-all">
                        {{ trans('icore::default.select_all') }}
                    </label>
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
        @can('admin.categories.delete')
        <div class="select-action rounded">
            <button 
                type="button"             
                class="btn btn-danger submit" 
                data-toggle="confirmation"
                data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
                data-btn-ok-icon-class="fas fa-check mr-1"
                data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover" 
                data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                data-btn-cancel-icon-class="fas fa-ban mr-1"
                data-title="{{ trans('icore::categories.confirm') }}"
            >
                <i class="far fa-trash-alt"></i>
                <span class="d-none d-sm-inline">
                    {{ trans('icore::default.delete_global') }}
                </span>
            </button>
        </div>
        @endcan
    </form>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>

@component('icore::admin.partials.modal')
@slot('modal_id', 'edit-modal')
@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::categories.route.edit') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'edit-position-modal')
@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('icore::categories.route.edit_position') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'create-modal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('icore::categories.route.create') }}</span>
@endslot
@endcomponent

@endsection
