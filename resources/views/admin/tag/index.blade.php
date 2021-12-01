@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [
        trans('icore::tags.route.index'),
        $tags->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $tags->currentPage()])
            : null
    ],
    'desc' => [trans('icore::tags.route.index')],
    'keys' => [trans('icore::tags.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('icore::tags.route.index') }}
</li>
@endsection

@section('content')
<div id="filter-content">
    <h1 class="h5 border-bottom pb-2 d-flex">
        <div class="mr-auto my-auto">
            <i class="fas fa-fw fa-hashtag"></i>
            <span> {{ trans('icore::tags.route.index') }}</span>
        </div>
        @can('admin.tags.create')
        <div class="ml-auto text-right">
            <div class="responsive-btn-group">
                <button 
                    type="button" 
                    class="btn btn-primary text-nowrap create" 
                    data-toggle="modal"
                    data-route="{{ route('admin.tag.create') }}" 
                    data-target="#create-modal"
                >
                    <i class="far fa-plus-square"></i>
                    <span class="d-none d-sm-inline">
                        {{ trans('icore::default.create') }}
                    </span>
                </button>
            </div>
        </div>
        @endcan
    </h1>
    <div>
        @include('icore::admin.tag.partials.filter')
        @if ($tags->isNotEmpty())
        <form 
            action="{{ route('admin.tag.destroy_global') }}" 
            method="post"
            id="select-form"
        >
            @csrf
            @method('delete')
            @can('admin.tags.delete')
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
                @foreach ($tags as $tag)
                    @include('icore::admin.tag.partials.tag', [
                        'tag' => $tag
                    ])
                @endforeach
                @include('icore::admin.partials.pagination', [
                    'items' => $tags
                ])
            </div>
            @can('admin.tags.delete')
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
                    data-title="{{ trans('icore::default.confirm') }}"
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
</div>

@include('icore::admin.tag.partials.filter_filter')

@component('icore::admin.partials.modal')
@slot('modal_id', 'edit-modal')
@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::tags.route.edit') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'create-modal')
@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('icore::tags.route.create') }}</span>
@endslot
@endcomponent

@endsection
