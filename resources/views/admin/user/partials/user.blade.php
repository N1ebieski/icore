<div 
    id="row{{ $user->id }}" 
    class="row border-bottom py-3 position-relative transition"
    data-id="{{ $user->id }}"
>
    <div class="col my-auto d-flex justify-content-between">
        @role('super-admin')
        @can('actionSelf', $user)
        <div class="custom-control custom-checkbox">
            <input 
                name="select[]" 
                type="checkbox" 
                class="custom-control-input select" 
                id="select{{ $user->id }}" 
                value="{{ $user->id }}"
            >
            <label class="custom-control-label" for="select{{ $user->id }}">
        @endcan
        @endrole
            <ul class="list-unstyled mb-0 pb-0">
                <li>
                    <span>{{ $user->name }}</span>
                    @foreach ($user->roles as $user_role)
                    <span class="badge badge-success">{{ $user_role->name }}</span>
                    @endforeach
                    @if (!$user->socialites->isEmpty())
                    @foreach ($user->socialites as $user_socialite)
                    <span class="badge badge-primary">
                        <i class="fab fa-{{ $user_socialite->provider_name }}"></i> 
                        <span>{{ $user_socialite->provider_name }}</span>
                    </span>
                    @endforeach
                    @endif
                </li>
                <li class="text-break" style="word-break:break-word">
                    {{ $user->email }}
                </li>
                @if (!is_null($user->ip))
                <li>
                    {{ $user->ip}}
                </li>
                @endif
                <li>
                    <small>{{ trans('icore::filter.created_at') }}: {{ $user->created_at_diff }}</small>
                </li>
                <li>
                    <small>{{ trans('icore::filter.updated_at') }}: {{ $user->updated_at_diff }}</small>
                </li>
            </ul>
        @role('super-admin')
        @can('actionSelf', $user)
            </label>
        </div>
        @endcan
        @endrole
        <div class="text-right ml-3">
            <div class="responsive-btn-group">
                @role('super-admin')
                @can('actionSelf', $user)
                <button 
                    data-toggle="modal" 
                    data-target="#edit-modal"
                    data-route="{{ route('admin.user.edit', ['user' => $user->id]) }}"
                    type="button" 
                    class="btn btn-primary edit"
                >
                    <i class="far fa-edit"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.edit') }}</span>
                </button>
                @endcan
                @can('actionSelf', $user)
                <button 
                    data-status="1" 
                    type="button" 
                    class="btn btn-success status"
                    data-route="{{ route('admin.user.update_status', ['user' => $user->id]) }}"
                    {{ $user->status == 1 ? 'disabled' : '' }}
                >
                    <i class="fas fa-toggle-on"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.active') }}</span>
                </button>
                <button 
                    data-status="0" 
                    type="button" 
                    class="btn btn-warning status"
                    data-route="{{ route('admin.user.update_status', ['user' => $user->id]) }}"
                    {{ $user->status == 0 ? 'disabled' : '' }}
                >
                    <i class="fas fa-toggle-off"></i>
                    <span class="d-none d-sm-inline">{{ trans('icore::default.inactive') }}</span>
                </button>
                @endcan
                @can('actionSelf', $user)
                <div class="btn-group-vertical">
                    <button 
                        type="button"                     
                        class="btn btn-danger" 
                        data-status="delete" 
                        data-toggle="confirmation"
                        data-route="{{ route('admin.user.destroy', ['user' => $user->id]) }}" 
                        data-id="{{ $user->id }}"
                        data-btn-ok-label=" {{ trans('icore::default.yes') }}" 
                        data-btn-ok-icon-class="fas fa-check mr-1" 
                        data-id="{{ $user->id }}"
                        data-btn-ok-class="btn h-100 d-flex justify-content-center btn-primary btn-popover destroy" 
                        data-btn-cancel-label=" {{ trans('icore::default.cancel') }}"
                        data-btn-cancel-class="btn h-100 d-flex justify-content-center btn-secondary btn-popover" 
                        data-btn-cancel-icon-class="fas fa-ban mr-1"
                        data-title="{{ trans('icore::default.confirm') }}"
                    >
                        <i class="far fa-trash-alt"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.delete') }}</span>
                    </button>
                    <button 
                        type="button" 
                        class="btn btn-dark create"
                        data-route="{{ route('admin.banmodel.user.create', ['user' => $user->id]) }}"
                        data-toggle="modal" 
                        data-target="#create-banmodel-user-modal"
                    >
                        <i class="fas fa-user-slash"></i>
                        <span class="d-none d-sm-inline">{{ trans('icore::default.ban') }}</span>
                    </button>
                </div>
                @endcan
                @endrole
            </div>
        </div>
    </div>
</div>
