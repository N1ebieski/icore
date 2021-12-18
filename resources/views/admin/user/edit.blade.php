@component('icore::admin.partials.modal')

@slot('modal_id', 'edit-modal')

@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::users.route.edit') }}</span>
@endslot

@slot('modal_body')
<form 
    id="edit-user"
    data-route="{{ route('admin.user.update', ['user' => $user->id]) }}"
    data-id="{{ $user->id }}" 
>
    <div class="form-group">
        <label for="name">
            {{ trans('icore::auth.name.label') }}
        </label>
        <input 
            type="text" 
            value="{{ $user->name }}" 
            name="name"
            class="form-control" 
            id="name"
        >
    </div>
    <div class="form-group">
        <label for="email">
            {{ trans('icore::auth.address.label') }}
        </label>
        <input 
            type="email" 
            value="{{ $user->email }}" 
            name="email"
            class="form-control" 
            id="email"
        >
    </div>
    <div class="form-group">
        <label for="roles">
            {{ trans('icore::users.roles') }}
        </label>
        <input type="hidden" name="roles" value="">
        <select multiple class="form-control custom-select" id="roles" name="roles[]">
            @foreach ($roles as $role)
            <option 
                value="{{ $role->name }}"
                @foreach ($user->roles as $user_role)
                {{ ($role->id === $user_role->id) ? 'selected' : '' }}
                @endforeach
            >
                {{ $role->name }}
            </option>
            @endforeach
        </select>
    </div>
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary update"
        form="edit-user"
    >
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button 
        type="button" 
        class="btn btn-secondary" 
        data-dismiss="modal"
    >
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</div>
@endslot

@endcomponent
