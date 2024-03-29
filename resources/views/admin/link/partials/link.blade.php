<div 
    id="row{{ $link->id }}" 
    class="row border-bottom py-3 position-relative transition"
    data-id="{{ $link->id }}"
>
    <div class="col my-auto d-flex justify-content-between">
        <ul class="list-unstyled mb-0 pb-0">
            <li>
                <span>
                    <a 
                        href="#" 
                        class="edit" 
                        data-route="{{ route('admin.link.edit_position', [$link->id]) }}"
                        data-toggle="modal" 
                        data-target="#edit-position-modal" 
                        role="button"
                    >
                        <span id="position" class="badge badge-pill badge-primary">{{ $link->position + 1 }}</span>
                    </a>
                </span>
                <span>
                    <a 
                        href="{{ $link->url }}" 
                        target="_blank" 
                        title="{{ $link->name }}"
                        rel="noopener"
                    >
                        {{ $link->name }}
                    </a>
                </span>
            </li>
            @if ($link->img_url !== null)
            <li class="my-1">
                <a 
                    href="{{ $link->url }}" 
                    target="_blank"
                >
                    <img class="img-fluid" src="{{ app('filesystem')->url($link->img_url) }}">
                </a>
            </li>
            @endif
            <li>
                <small>{{ trans('icore::filter.created_at') }}: {{ $link->created_at_diff }}</small>
            </li>
            <li>
                <small>{{ trans('icore::filter.updated_at') }}: {{ $link->updated_at_diff }}</small>
            </li>
        </ul>
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('admin.links.edit')
                <button 
                    data-toggle="modal" 
                    data-target="#edit-modal"
                    data-route="{{ route('admin.link.edit', [$link->id]) }}"
                    type="button" 
                    class="btn btn-primary edit"
                >
                    <i class="far fa-edit"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.edit') }}</span>
                </button>
                @endcan
                @can('admin.links.delete')
                <button 
                    type="button"                
                    class="btn btn-danger" 
                    data-status="delete" 
                    data-toggle="confirmation"
                    data-route="{{ route('admin.link.destroy', [$link->id]) }}" data-id="{{ $link->id }}" 
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
