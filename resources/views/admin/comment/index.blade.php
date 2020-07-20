@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [
        trans('icore::comments.route.index'),
        $comments->currentPage() > 1 ?
            trans('icore::pagination.page', ['num' => $comments->currentPage()])
            : null
    ],
    'desc' => [trans('icore::comments.route.index')],
    'keys' => [trans('icore::comments.route.index')]
])

@section('breadcrumb')
<li class="breadcrumb-item">{{ trans('icore::comments.route.index') }}</li>
<li class="breadcrumb-item active" aria-current="page">{{ trans("icore::comments.{$model->poli}.{$model->poli}") }}</li>
@endsection

@section('content')
<h1 class="h5 border-bottom pb-2">
    <i class="fas fa-fw fa-comments"></i>&nbsp;{{ trans('icore::comments.route.index') }}
</h1>
<div id="filterContent">
    @include('icore::admin.comment.partials.filter')
    @if ($comments->isNotEmpty())
    <form action="{{ route('admin.comment.destroy_global') }}" method="post" id="selectForm">
    @csrf
    @method('delete')
        @can('admin.comments.delete')
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
            @foreach ($comments as $comment)
                @include('icore::admin.comment.partials.comment', ['comment' => $comment])
            @endforeach
            @include('icore::admin.partials.pagination', ['items' => $comments])
        </div>
        @can('admin.comments.delete')
        <div class="select-action rounded">
            <button class="btn btn-danger submit" data-toggle="confirmation"
            type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check mr-1"
            data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover" 
            data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
            data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
            data-btn-cancel-icon-class="fas fa-ban mr-1"
            data-title="{{ trans('icore::comments.confirm') }}">
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
@slot('modal_id', 'showCommentModal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="far fa-comments"></i>
<span> {{ trans('icore::comments.route.show_disqus') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'editCommentModal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::comments.route.edit') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'createCommentModal')
@slot('modal_size', 'modal-lg')
@slot('modal_title')
<i class="far fa-comment"></i>
<span> {{ trans('icore::comments.route.create') }}</span>
@endslot
@endcomponent

@component('icore::admin.partials.modal')
@slot('modal_id', 'showReportCommentModal')
@slot('modal_title')
<span>{{ trans('icore::reports.route.show') }}</span>
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
