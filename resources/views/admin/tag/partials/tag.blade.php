<div 
    id="row{{ $tag->tag_id }}" 
    class="row border-bottom py-3 position-relative transition"
    data-id="{{ $tag->tag_id }}"
>
    <div class="col my-auto d-flex justify-content-between">
        @can('admin.tags.delete')
        <div class="custom-control custom-checkbox">
            <input 
                name="select[]" 
                type="checkbox" 
                class="custom-control-input select" 
                id="select{{ $tag->tag_id }}" 
                value="{{ $tag->tag_id }}"
            >
            <label class="custom-control-label" for="select{{ $tag->tag_id }}">
        @endcan    
            <ul class="list-unstyled mb-0 pb-0">
                <li>{{ $tag->name }}</li>
                <li>
                    <small>{{ trans('icore::filter.created_at') }}: {{ $tag->created_at_diff }}</small>
                </li>
                <li>
                    <small>{{ trans('icore::filter.updated_at') }}: {{ $tag->updated_at_diff }}</small>
                </li>
            </ul>
        @can('admin.tags.delete')
            </label>
        </div>
        @endcan            
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('admin.tags.edit')
                <div class="btn-group-vertical">
                    <button 
                        data-toggle="modal" 
                        data-target="#edit-modal"
                        data-route="{{ route("admin.tag.edit", [$tag->tag_id]) }}"
                        type="button" 
                        class="btn btn-primary edit"
                    >
                        <i class="far fa-edit"></i>
                        <span class="d-none d-sm-inline">
                            {{ trans('icore::default.edit') }}
                        </span>
                    </button>
                </div>
                @endcan
                @can('admin.tags.delete')
                <button 
                    type="button"                
                    class="btn btn-danger" 
                    data-status="delete" 
                    data-toggle="confirmation"
                    data-route="{{ route('admin.tag.destroy', [$tag->tag_id]) }}" 
                    data-id="{{ $tag->tag_id }}"
                    data-btn-ok-label="{{ trans('icore::default.yes') }}" 
                    data-btn-ok-icon-class="fas fa-check mr-1"
                    data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroy" 
                    data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                    data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover"
                    data-btn-cancel-icon-class="fas fa-ban mr-1"
                    data-title="{{ trans('icore::default.confirm') }}"
                >
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-sm-inline">
                        {{ trans('icore::default.delete') }}
                    </span>
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
