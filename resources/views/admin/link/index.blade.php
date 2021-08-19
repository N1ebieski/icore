@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [
        trans("icore::links.{$type}.route.index"),
        $links->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $links->currentPage()])
            : null
    ],
    'desc' => [trans("icore::links.{$type}.route.index")],
    'keys' => [trans("icore::links.{$type}.route.index")]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    {{ trans('icore::links.route.index') }}
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans("icore::links.{$type}.route.index") }}
</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fas fa-link"></i>
        <span> {{ trans("icore::links.{$type}.route.index") }}</span>
    </div>
    @can('admin.links.create')
    <div class="ml-auto text-right">
        <div class="responsive-btn-group">
            <a 
                href="#" 
                data-route="{{ route('admin.link.create', [$type]) }}" 
                role="button"
                class="btn btn-primary text-nowrap create" 
                data-toggle="modal" 
                data-target="#create-modal"
            >
                <i class="far fa-plus-square"></i>
                <span class="d-none d-sm-inline">
                    {{ trans('icore::default.create') }}
                </span>
            </a>
        </div>
    </div>
    @endcan
</h1>
<div id="filter-content">
    @if ($links->isNotEmpty())
    <div id="infinite-scroll">
        @foreach ($links as $link)
            @include('icore::admin.link.partials.link')
        @endforeach
        @include('icore::admin.partials.pagination', ['items' => $links])
    </div>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>

@component('icore::admin.partials.modal')
@slot('modal_id', 'edit-modal')
@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::links.route.edit') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'create-modal')
@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('icore::links.route.create') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'edit-position-modal')
@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('icore::links.route.edit_position') }}</span>
@endslot
@endcomponent

@endsection
