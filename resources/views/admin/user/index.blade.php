@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [trans('icore::users.route.index'), trans('icore::pagination.page', ['num' => $users->currentPage()])],
    'desc' => [trans('icore::users.route.index')],
    'keys' => [trans('icore::users.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.home.index') }}">{{ trans('icore::home.route.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::users.route.index') }}</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fas fa-fw fa-users"></i>
        <span>{{ trans('icore::users.route.index') }}</span>
    </div>
    @role('super-admin')
    <div class="ml-auto text-right">
        <button type="button" class="btn btn-primary text-nowrap create"
        data-route="{{ route('admin.user.create') }}"
        data-toggle="modal" data-target="#createModal">
            <i class="far fa-plus-square"></i>
            <span class="d-none d-sm-inline">{{ trans('icore::users.route.create') }}</span>
        </button>
    </div>
    @endrole
</h1>
<div id="filterContent">
    @include('icore::admin.user.partials.filter')
    @if ($users->isNotEmpty())
    <form action="{{ route('admin.user.destroy_global') }}" method="post" id="selectForm">
    @csrf
    @method('delete')
        @role('super-admin')
        <div class="row my-2">
            <div class="col my-auto">
                <div class="custom-checkbox custom-control">
                    <input type="checkbox" class="custom-control-input" id="selectAll">
                    <label class="custom-control-label" for="selectAll">{{ trans('icore::default.select_all') }}</label>
                </div>
            </div>
        </div>
        @endrole
        <div id="infinite-scroll">
            @foreach ($users as $user)
                @include('icore::admin.user.partials.user')
            @endforeach
            @include('icore::admin.partials.pagination', ['items' => $users])
        </div>
        @role('super-admin')
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
        @endrole
    </form>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>

@component('icore::admin.partials.modal')
@slot('modal_id', 'editModal')
@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::users.route.edit') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'createModal')
@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('icore::users.route.create') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'createBanUserModal')
@slot('modal_title')
<i class="fas fa-user-slash"></i>
<span> {{ trans('icore::bans.route.create') }}</span>
@endslot
@endcomponent

@endsection
