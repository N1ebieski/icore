@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [
        trans("icore::bans.value.{$type}.route.index"),
        $bans->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $bans->currentPage()])
            : null
    ],
    'desc' => [trans("icore::bans.value.{$type}.route.index")],
    'keys' => [trans("icore::bans.value.{$type}.route.index")]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    {{ trans('icore::bans.route.index') }}
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans("icore::bans.value.{$type}.route.index") }}
</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fas fa-user-slash"></i>
        <span>{{ trans("icore::bans.value.{$type}.route.index") }}</span>
    </div>
    @can('admin.bans.create')
    <div class="ml-auto text-right">
        <a 
            href="#" 
            data-route="{{ route('admin.banvalue.create', [$type]) }}" 
            role="button"
            class="btn btn-primary text-nowrap create" 
            data-toggle="modal" 
            data-target="#create-modal"
        >
            <i class="far fa-plus-square"></i>
            <span class="d-none d-sm-inline">{{ trans('icore::bans.value.create') }}</span>
        </a>
    </div>
    @endcan
</h1>
<div id="filter-content">
    @include('icore::admin.banvalue.partials.filter')

    @if ($bans->isNotEmpty())
    <form 
        action="{{ route('admin.banvalue.destroy_global') }}" 
        method="post" 
        id="select-form"
    >
        @csrf
        @method('delete')
        @can('admin.bans.delete')
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
            @foreach ($bans as $ban)
                @include('icore::admin.banvalue.partials.ban')
            @endforeach
            @include('icore::admin.partials.pagination', ['items' => $bans])
        </div>
        @can('admin.bans.delete')
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

@component('icore::admin.partials.modal')
@slot('modal_id', 'edit-modal')
@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::bans.route.edit') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'create-modal')
@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('icore::bans.route.create') }}</span>
@endslot
@endcomponent

@endsection
