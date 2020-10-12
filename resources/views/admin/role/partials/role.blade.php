<div 
    id="row{{ $role->id }}" 
    class="row border-bottom py-3 position-relative transition"
    data-id="{{ $role->id }}"
>
    <div class="col my-auto d-flex justify-content-between">
        <ul class="list-unstyled mb-0 pb-0">
            <li>
                {{ $role->name }}
            </li>
            <li>
                <small>{{ trans('icore::filter.created_at') }}: {{ $role->created_at_diff }}</small>
            </li>
            <li>
                <small>{{ trans('icore::filter.updated_at') }}: {{ $role->updated_at_diff }}</small>
            </li>
        </ul>
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @role('super-admin')
                @if ($role->isEditNotDefault())
                <a 
                    class="btn btn-primary align-bottom" 
                    href="{{ route('admin.role.edit', [$role->id]) }}"
                    role="button" 
                    target="_blank" 
                    rel="noopener"
                >
                    <i class="fas fa-edit"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.edit') }}</span>
                </a>
                @endif
                @if ($role->isDeleteNotDefault())
                <form 
                    action="{{ route('admin.role.destroy', [$role->id]) }}" 
                    method="post"
                >
                    @csrf
                    @method('delete')
                    <button 
                        type="button" 
                        class="btn btn-danger submit" 
                        data-status="delete" 
                        data-toggle="confirmation"
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
                </form>
                @endif
                @endrole
            </div>
        </div>
    </div>
</div>
