<div 
    id="row{{ $comment->id }}" 
    class="row py-3 border-bottom position-relative transition"
    data-id="{{ $comment->id }}"
>
    <div class="col my-auto d-flex justify-content-between">
        @can('admin.comments.delete')
        <div class="custom-control custom-checkbox">
            <input 
                name="select[]" 
                type="checkbox" 
                class="custom-control-input select" 
                id="select{{ $comment->id }}" 
                value="{{ $comment->id }}"
            >
            <label class="custom-control-label" for="select{{ $comment->id }}">
        @endcan
                <ul class="list-unstyled mb-0 pb-0">
                    @if (isset($comment->morph))
                    <li>
                        <span>
                            <a 
                                class="show" 
                                href="#" 
                                data-toggle="modal" data-target="#show-comment-modal" 
                                title="{{ trans('icore::comments.disqus', ['name' => $comment->morph->title]) }}"
                                data-route="{{ route('admin.comment.show', ['comment' => $comment->id]) }}"
                            >
                                {{ trans('icore::comments.disqus', ['name' => $comment->morph->title]) }}
                            </a>
                        </span>
                        @if ($comment->reports_count > 0)
                        <span>
                            <a 
                                href="#" 
                                class="badge badge-danger show" 
                                data-toggle="modal"
                                data-route="{{ route('admin.report.comment.show', ['comment' => $comment->id]) }}"
                                data-target="#show-report-comment-modal"
                            >
                                {{ trans('icore::comments.reports') }}: {{ $comment->reports_count }}
                            </a>
                        </span>
                        @endif
                    </li>
                    @endif
                    <li>
                        @if ($comment->censored == true)
                        <em>
                        @endif
                        {!! $comment->content_as_html !!}
                        @if ($comment->censored == true)
                        </em>
                        @endif
                    </li>
                    @if ($comment->user)
                    <li>
                        <small>
                            <span>{{ trans('icore::comments.author') }}:</span>
                            <span>
                                <a 
                                    href="{{ route("admin.comment.{$comment->poli}.index", ['filter[author]' => $comment->user->id]) }}"
                                    title="{{ $comment->user->name }}"
                                >
                                    {{ $comment->user->name }}
                                </a>
                            </span>
                            <span>
                                <a 
                                    href="{{ route("admin.comment.{$comment->poli}.index", ['filter[search]' => "user:\"{$comment->user->ip}\""]) }}"
                                    title="{{ $comment->user->ip }}"
                                >
                                    {{ $comment->user->ip }}
                                </a>
                            </span>
                        </small>
                    </li>
                    @endif
                    <li>
                        <small>{{ trans('icore::filter.created_at') }}: {{ $comment->created_at_diff }}</small>
                    </li>
                    <li>
                        <small>{{ trans('icore::filter.updated_at') }}: {{ $comment->updated_at_diff }}</small>
                    </li>
                </ul>
        @can('admin.comments.delete')
            </label>
        </div>
        @endcan
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                <div class="btn-group-vertical">
                    @can('admin.comments.edit')
                    <button 
                        data-toggle="modal" 
                        data-target="#edit-comment-modal"
                        data-route="{{ route('admin.comment.edit', ['comment' => $comment->id]) }}"
                        type="button" 
                        class="btn btn-primary edit"
                    >
                        <i class="far fa-edit"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.edit') }}</span>
                    </button>
                    @endcan
                    @can('admin.comments.create')
                    <button 
                        data-toggle="modal" 
                        data-target="#create-comment-modal"
                        data-route="{{ route("admin.comment.{$comment->poli}.create", [$comment->model_id, 'parent_id' => $comment->id]) }}" 
                        type="button" 
                        class="btn btn-primary answer create"
                        {{ ($comment->isCommentable()) ? null : 'disabled' }}
                    >
                        <i class="far fa-comment"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.answer') }}</span>
                    </button>
                    @endcan
                </div>
                @can('admin.comments.status')
                <div class="btn-group-vertical">
                    <button 
                        data-status="1" 
                        type="button" 
                        class="btn btn-success status-comment"
                        data-route="{{ route('admin.comment.update_status', ['comment' => $comment->id]) }}"
                        {{ $comment->status == 1 ? 'disabled' : '' }}
                    >
                        <i class="fas fa-toggle-on"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.active') }}</span>
                    </button>
                    <button 
                        data-censored="0" 
                        type="button" 
                        class="btn btn-success censore-comment"
                        data-route="{{ route('admin.comment.update_censored', ['comment' => $comment->id]) }}"
                        {{ $comment->censored == 0 ? 'disabled' : '' }}
                    >
                        <i class="far fa-eye"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.uncensored') }}</span>
                    </button>
                </div>
                <div class="btn-group-vertical">
                    <button 
                        data-status="0" 
                        type="button" 
                        class="btn btn-warning status-comment"
                        data-route="{{ route('admin.comment.update_status', ['comment' => $comment->id]) }}"
                        {{ $comment->status == 0 ? 'disabled' : '' }}
                    >
                        <i class="fas fa-toggle-off"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.inactive') }}</span>
                    </button>
                    <button 
                        data-censored="1" 
                        type="button" 
                        class="btn btn-warning censore-comment"
                        data-route="{{ route('admin.comment.update_censored', ['comment' => $comment->id]) }}"
                        {{ $comment->censored == 1 ? 'disabled' : '' }}
                    >
                        <i class="far fa-eye-slash"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.censored') }}</span>
                    </button>
                </div>
                @endcan
                @if ($comment->user)
                <div class="btn-group-vertical">
                @endif
                    @can('admin.comments.delete')
                    <button 
                        class="btn btn-danger" 
                        data-status="delete" 
                        data-toggle="confirmation"
                        data-route="{{ route('admin.comment.destroy', ['comment' => $comment->id]) }}"
                        data-id="{{ $comment->id }}"
                        type="button" 
                        data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
                        data-btn-ok-icon-class="fas fa-check mr-1"
                        data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroy-comment" 
                        data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                        data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                        data-btn-cancel-icon-class="fas fa-ban mr-1"
                        data-title="{{ trans('icore::comments.confirm') }}"
                    >
                        <i class="far fa-trash-alt"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.delete') }}</span>
                    </button>
                    @endcan
                    @can('admin.bans.create')
                    @if ($comment->user)
                    <button 
                        type="button" 
                        class="btn btn-dark create"
                        data-route="{{ route('admin.banmodel.user.create', [$comment->user->id]) }}"
                        data-toggle="modal" 
                        data-target="#create-banuser-modal"
                    >
                        <i class="fas fa-user-slash"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.ban') }}</span>
                    </button>
                    @endif
                    @endcan
                @if ($comment->user)    
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
