@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [
        trans('icore::posts.route.index'),
        $posts->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $posts->currentPage()])
            : null
    ],
    'desc' => [trans('icore::posts.route.index')],
    'keys' => [trans('icore::posts.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('icore::posts.route.index') }}
</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fas fa-fw fa-blog"></i>
        <span>{{ trans('icore::posts.route.index') }}</span>
    </div>
    @can('admin.posts.create')
    <div class="ml-auto text-right">
        <a 
            href="{{ route('admin.post.create') }}" 
            role="button" 
            class="btn btn-primary text-nowrap"
        >
            <i class="far fa-plus-square"></i>
            <span class="d-none d-sm-inline">{{ trans('icore::posts.create') }}</span>
        </a>
    </div>
    @endcan
</h1>
<div id="filterContent">
    @include('icore::admin.post.partials.filter')
    @if ($posts->isNotEmpty())
    <form 
        action="{{ route('admin.post.destroy_global') }}" 
        method="post" 
        id="selectForm"
    >
        @csrf
        @method('delete')
        @can('destroy users')
        <div class="row my-2">
            <div class="col my-auto">
                <div class="custom-checkbox custom-control">
                    <input 
                        type="checkbox" 
                        class="custom-control-input" 
                        id="selectAll"
                    >
                    <label class="custom-control-label" for="selectAll">
                        {{ trans('icore::default.select_all') }}
                    </label>
                </div>
            </div>
        </div>
        @endcan
        <div id="infinite-scroll">
            @foreach ($posts as $post)
                @include('icore::admin.post.partials.post')
            @endforeach
            @include('icore::admin.partials.pagination', ['items' => $posts])
        </div>
        @can('admin.posts.delete')
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
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::posts.route.edit') }}</span>
@endslot
@endcomponent

@endsection
