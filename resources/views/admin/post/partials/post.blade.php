<div 
    id="row{{ $post->id }}" 
    class="row border-bottom py-3 position-relative transition"
    data-id="{{ $post->id }}"
>
    <div class="col my-auto d-flex justify-content-between">
        @can('admin.posts.delete')
        <div class="custom-control custom-checkbox">
            <input 
                name="select[]" 
                type="checkbox" 
                class="custom-control-input select" 
                id="select{{ $post->id }}" 
                value="{{ $post->id }}"
            >
            <label class="custom-control-label" for="select{{ $post->id }}">
        @endcan
                <ul class="list-unstyled mb-0 pb-0">
                    <li>
                        <a 
                            href="{{ route('admin.post.edit_full', ['post' => $post->id]) }}" 
                            target="_blank" 
                            title="{{ $post->title }}" 
                            rel="noopener"
                        >
                            {{ $post->title }}
                        </a>
                    </li>
                    <li class="text-break" style="word-break:break-word">
                        {!! $post->shortContent !!}...
                    </li>
                    <li>
                        <small>{{ $post->tag_list }}</small>
                    </li>
                    <li>
                        <small>{{ trans('icore::posts.published_at.label') }}: {{ $post->published_at_diff }}</small>
                    </li>
                    <li>
                        <small>{{ trans('icore::filter.created_at') }}: {{ $post->created_at_diff }}</small>
                    </li>
                    <li>
                        <small>{{ trans('icore::filter.updated_at') }}: {{ $post->updated_at_diff }}</small>
                    </li>
                </ul>
        @can('admin.posts.delete')
            </label>
        </div>
        @endcan
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('admin.posts.edit')
                <div class="btn-group-vertical">
                    <button 
                        data-toggle="modal" 
                        data-target="#edit-modal"
                        data-route="{{ route('admin.post.edit', ['post' => $post->id]) }}"
                        type="button" 
                        class="btn btn-primary edit"
                    >
                        <i class="far fa-edit"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.edit') }}</span>
                    </button>
                    <a 
                        class="btn btn-primary align-bottom" 
                        href="{{ route('admin.post.edit_full', ['post' => $post->id]) }}"
                        role="button" 
                        target="_blank" 
                        rel="noopener"
                    >
                        <i class="fas fa-edit"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.editFull') }}</span>
                    </a>
                </div>
                @endcan
                @can('admin.posts.status')
                <button 
                    data-status="{{ Post\Status::ACTIVE }}" 
                    type="button" 
                    class="btn btn-success status"
                    data-route="{{ route('admin.post.update_status', ['post' => $post->id]) }}"
                    {{ $post->status->isActive() ? 'disabled' : '' }}
                >
                    <i class="fas fa-toggle-on"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.active') }}</span>
                </button>
                <button 
                    data-status="{{ Post\Status::INACTIVE }}" 
                    type="button" 
                    class="btn btn-warning status"
                    data-route="{{ route('admin.post.update_status', ['post' => $post->id]) }}"
                    {{ $post->status->isInactive() ? 'disabled' : '' }}
                >
                    <i class="fas fa-toggle-off"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.inactive') }}</span>
                </button>
                @endcan
                @can('admin.posts.delete')
                <button 
                    type="button" 
                    class="btn btn-danger" 
                    data-status="delete" 
                    data-toggle="confirmation"
                    data-route="{{ route('admin.post.destroy', ['post' => $post->id]) }}" 
                    data-id="{{ $post->id }}"
                    data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
                    data-btn-ok-icon-class="fas fa-check mr-1"
                    data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroy" 
                    data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                    data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                    data-btn-cancel-icon-class="fas fa-ban mr-1"
                    data-title="{{ trans('icore::default.confirm') }}"
                >
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.delete') }}</span>
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
