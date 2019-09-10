<div id="row{{ $role->id }}" class="row border-bottom py-3 position-relative transition">
    <div class="col my-auto d-flex justify-content-between">
        <ul class="list-unstyled mb-0 pb-0">
            <li>{{ $role->name }}</li>
            <li><small>{{ trans('icore::filter.created_at') }}:&nbsp;{{ $role->created_at_diff }}</small></li>
            <li><small>{{ trans('icore::filter.updated_at') }}:&nbsp;{{ $role->updated_at_diff }}</small></li>
        </ul>
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @role('super-admin')
                @can('editDefault', $role)
                <a class="btn btn-primary align-bottom" href="{{ route('admin.role.edit', [$role->id]) }}"
                role="button" target="_blank">
                    <i class="fas fa-edit"></i>
                    <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.edit') }}</span>
                </a>
                @endcan
                @can('deleteDefault', $role)
                <form action="{{ route('admin.role.destroy', [$role->id]) }}" method="post">
                    @csrf
                    @method('delete')
                    <button class="btn btn-danger submit" data-status="delete" data-toggle="confirmation"
                    type="button" data-btn-ok-label=" {{ trans('icore::default.yes') }}" data-btn-ok-icon-class="fas fa-check"
                    data-btn-ok-class="btn-primary btn-popover destroy" data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                    data-btn-cancel-class="btn-secondary btn-popover" data-btn-cancel-icon-class="fas fa-ban"
                    data-title="{{ trans('icore::default.confirm') }}">
                        <i class="far fa-trash-alt"></i>
                        <span class="d-none d-sm-inline">&nbsp;{{ trans('icore::default.delete') }}</span>
                    </button>
                </form>
                @endcan
                @endrole
            </div>
        </div>
    </div>
</div>
