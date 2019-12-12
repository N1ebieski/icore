<div id="row{{ $link->id }}" class="row border-bottom py-3 position-relative transition">
    <div class="col my-auto d-flex justify-content-between">
        <ul class="list-unstyled mb-0 pb-0">
            <li>
                <a href="#" class="edit" data-route="{{ route('admin.link.edit_position', [$link->id]) }}"
                data-toggle="modal" data-target="#editPositionModal" role="button">
                    <span id="position" class="badge badge-pill badge-primary">{{ $link->position + 1 }}</span>
                </a>
                <span> <a href="{{ $link->url }}" target="_blank">{{ $link->name }}</a></span>
            </li>
            @if ($link->img_url !== null)
            <li class="my-1">
                <a href="{{ $link->url }}" target="_blank"><img class="img-fluid" src="{{ Storage::url($link->img_url) }}"></a>
            </li>
            @endif
            <li><small>{{ trans('icore::filter.created_at') }}:&nbsp;{{ $link->created_at_diff }}</small></li>
            <li><small>{{ trans('icore::filter.updated_at') }}:&nbsp;{{ $link->updated_at_diff }}</small></li>
        </ul>
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('edit links')
                <button data-toggle="modal" data-target="#editModal"
                data-route="{{ route('admin.link.edit', [$link->id]) }}"
                type="button" class="btn btn-primary edit">
                    <i class="far fa-edit"></i>
                    <span class="d-none d-sm-inline"> {{ trans('icore::default.edit') }}</span>
                </button>
                @endcan
                @can('delete links')
                <button class="btn btn-danger" data-status="delete" data-toggle="confirmation"
                data-route="{{ route('admin.link.destroy', [$link->id]) }}" data-id="{{ $link->id }}"
                type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check"
                data-btn-ok-class="btn-primary btn-popover destroy" data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                data-btn-cancel-class="btn-secondary btn-popover" data-btn-cancel-icon-class="fas fa-link"
                data-title="{{ trans('icore::default.confirm') }}">
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-sm-inline"> {{ trans('icore::default.delete') }}</span>
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
