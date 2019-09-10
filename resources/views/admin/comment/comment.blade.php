<div id="row{{ $comment->id }}" class="row py-3 border-bottom position-relative transition">
    <div class="col my-auto d-flex justify-content-between">
        @can('destroy comments')
        <div class="custom-control custom-checkbox">
            <input name="select[]" type="checkbox" class="custom-control-input select" id="select{{ $comment->id }}" value="{{ $comment->id }}">
            <label class="custom-control-label" for="select{{ $comment->id }}">
        @endcan
                <ul class="list-unstyled mb-0 pb-0">
                    <li>
                        <a class="show" href="#" data-toggle="modal" data-target="#showCommentModal" data-route="{{ route('admin.comment.show', ['comment' => $comment->id]) }}">{{ trans('icore::comments.disqus', ['name' => $comment->model->title]) }}</a>
                        @if ($comment->reports_count > 0)
                        &nbsp;<a href="#" class="badge badge-danger show" data-toggle="modal"
                        data-route="{{ route('admin.report.comment.show', ['comment' => $comment->id]) }}"
                        data-target="#showReportCommentModal">
                            {{ trans('icore::comments.reports') }}: {{ $comment->reports_count }}
                        </a>
                        @endif
                    </li>
                    <li>
                        @if ($comment->censored == true)<em>@endif
                        {!! nl2br(e($comment->content_html)) !!}
                        @if ($comment->censored == true)</em>@endif
                    </li>
                    @if ($comment->user)
                    <li><small>{{ trans('icore::comments.author') }}: <a href="{{ route('admin.comment.'.$comment->poli.'.index', ['filter[author]' => $comment->user->id]) }}">{{ $comment->user->name }}</a></small></li>
                    @endif
                    <li><small>{{ trans('icore::filter.created_at') }}: {{ $comment->created_at_diff }}</small></li>
                    <li><small>{{ trans('icore::filter.updated_at') }}: {{ $comment->updated_at_diff }}</small></li>
                </ul>
        @can('destroy comments')
            </label>
        </div>
        @endcan
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                <div class="btn-group-vertical">
                    @can('edit comments')
                    <button data-toggle="modal" data-target="#editCommentModal"
                    data-route="{{ route('admin.comment.edit', ['comment' => $comment->id]) }}"
                    type="button" class="btn btn-primary edit">
                        <i class="far fa-edit"></i>
                        <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.edit') }}</span>
                    </button>
                    @endcan
                    @can('create comments')
                    <button data-toggle="modal" data-target="#createCommentModal"
                    data-route="{{ route('admin.comment.'.$comment->poli.'.create', [
                        $comment->model_id,
                        'parent_id' => $comment->id
                    ]) }}" type="button" class="btn btn-primary answer create"
                    {{ ($comment->status === 1 && (bool)$comment->model->comment === true) ? '' : 'disabled' }}>
                        <i class="far fa-comment"></i>
                        <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.answer') }}</span>
                    </button>
                    @endcan
                </div>
                @can('status comments')
                <div class="btn-group-vertical">
                    <button data-status="1" type="button" class="btn btn-success statusComment"
                    data-route="{{ route('admin.comment.update_status', ['comment' => $comment->id]) }}"
                    {{ $comment->status == 1 ? 'disabled' : '' }}>
                        <i class="fas fa-toggle-on"></i>
                        <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.active') }}</span>
                    </button>
                    <button data-censored="0" type="button" class="btn btn-success censoreComment"
                    data-route="{{ route('admin.comment.update_censored', ['comment' => $comment->id]) }}"
                    {{ $comment->censored == 0 ? 'disabled' : '' }}>
                        <i class="far fa-eye"></i>
                        <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.uncensored') }}</span>
                    </button>
                </div>
                <div class="btn-group-vertical">
                    <button data-status="0" type="button" class="btn btn-warning statusComment"
                    data-route="{{ route('admin.comment.update_status', ['comment' => $comment->id]) }}"
                    {{ $comment->status == 0 ? 'disabled' : '' }}>
                        <i class="fas fa-toggle-off"></i>
                        <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.inactive') }}</span>
                    </button>
                    <button data-censored="1" type="button" class="btn btn-warning censoreComment"
                    data-route="{{ route('admin.comment.update_censored', ['comment' => $comment->id]) }}"
                    {{ $comment->censored == 1 ? 'disabled' : '' }}>
                        <i class="far fa-eye-slash"></i>
                        <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.censored') }}</span>
                    </button>
                </div>
                @endcan
                <div class="btn-group-vertical">
                    @can('destroy comments')
                    <button class="btn btn-danger" data-status="delete" data-toggle="confirmation"
                    data-route="{{ route('admin.comment.destroy', ['comment' => $comment->id]) }}" data-id="{{ $comment->id }}"
                    type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check"
                    data-btn-ok-class="btn-primary btn-popover destroyComment" data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                    data-btn-cancel-class="btn-secondary btn-popover" data-btn-cancel-icon-class="fas fa-ban"
                    data-title="{{ trans('icore::comments.confirm') }}">
                        <i class="far fa-trash-alt"></i>
                        <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.delete') }}</span>
                    </button>
                    @endcan
                    @can('create bans')
                    <button type="button" class="btn btn-dark create"
                    data-route="{{ route('admin.banmodel.user.create', [$comment->user_id]) }}"
                    data-toggle="modal" data-target="#createBanUserModal">
                        <i class="fas fa-user-slash"></i>
                        <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.ban') }}</span>
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
