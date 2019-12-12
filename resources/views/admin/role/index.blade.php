@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [trans('icore::roles.page.index'), trans('icore::pagination.page', ['num' => $roles->currentPage()])],
    'desc' => [trans('icore::roles.page.index')],
    'keys' => [trans('icore::roles.page.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.home.index') }}">{{ trans('icore::home.page.index') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::roles.page.index') }}</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2 d-flex">
    <div class="mr-auto my-auto">
        <i class="fas fa-fw fa-unlock-alt"></i>&nbsp;{{ trans('icore::roles.page.index') }}
    </div>
    @role('super-admin')
    <div class="ml-auto text-right responsive-btn-group">
        <a href="{{ route('admin.role.create') }}" role="button" class="btn btn-primary text-nowrap">
            <i class="far fa-plus-square"></i>
            <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.create') }}</span>
        </a>
    </div>
    @endrole
</h1>
<div id="filterContent">
    @if ($roles->isNotEmpty())
    <div id="infinite-scroll">
        @foreach ($roles as $role)
            @include('icore::admin.role.partials.role')
        @endforeach
        @include('icore::admin.partials.pagination', ['items' => $roles])
    </div>
    @else
    <p>{{ trans('icore::default.empty') }}</p>
    @endif
</div>
@endsection
