<div id="row{{ $ban->id }}" class="row border-bottom py-3 position-relative transition"
data-id="{{ $ban->id }}">
    <div class="col my-auto d-flex justify-content-between">
        @can('delete bans')
        <div class="custom-control custom-checkbox">
            <input name="select[]" type="checkbox" class="custom-control-input select" id="select{{ $ban->id }}" value="{{ $ban->id }}">
            <label class="custom-control-label" for="select{{ $ban->id }}">
        @endcan
                <ul class="list-unstyled mb-0 pb-0">
                    <li>{{ $ban->value }}</li>
                    <li><small>{{ trans('icore::filter.created_at') }}:&nbsp;{{ $ban->created_at_diff }}</small></li>
                    <li><small>{{ trans('icore::filter.updated_at') }}:&nbsp;{{ $ban->updated_at_diff }}</small></li>
                </ul>
        @can('delete bans')
            </label>
        </div>
        @endcan
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @can('edit bans')
                <button data-toggle="modal" data-target="#editModal"
                data-route="{{ route('admin.banvalue.edit', [$ban->id]) }}"
                type="button" class="btn btn-primary edit">
                    <i class="far fa-edit"></i>
                    <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.edit') }}</span>
                </button>
                @endcan
                @can('delete bans')
                <button class="btn btn-danger" data-status="delete" data-toggle="confirmation"
                data-route="{{ route('admin.banvalue.destroy', [$ban->id]) }}" data-id="{{ $ban->id }}"
                type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check mr-1"
                data-btn-ok-class="btn h-100 d-flex align-items-center btn-primary btn-popover destroy" 
                data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                data-btn-cancel-class="btn h-100 d-flex align-items-center btn-secondary btn-popover" 
                data-btn-cancel-icon-class="fas fa-ban mr-1"
                data-title="{{ trans('icore::default.confirm') }}">
                    <i class="far fa-trash-alt"></i>
                    <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.delete') }}</span>
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
