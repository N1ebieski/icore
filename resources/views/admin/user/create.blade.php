<form data-route="{{ route('admin.user.store') }}" id="store">
    <div class="form-group">
        <label for="name">
            {{ trans('icore::auth.name.label') }}
        </label>
        <input 
            type="text" 
            value="{{ old('name') }}" 
            name="name"
            class="form-control" 
            id="name" 
            placeholder="{{ trans('icore::auth.name.placeholder') }}"
        >
    </div>
    <div class="form-group">
        <label for="email">
            {{ trans('icore::auth.address.label') }}
        </label>
        <input 
            type="email" 
            value="{{ old('email') }}" 
            name="email"
            class="form-control" 
            id="email" 
            placeholder="{{ trans('icore::auth.address.placeholder') }}"
        >
    </div>
    <div class="form-group">
        <label for="password">
            {{ trans('icore::auth.password') }}
        </label>
        <input 
            id="password" 
            type="password" 
            class="form-control" 
            name="password" 
            required
        >
    </div>
    <div class="form-group">
        <label for="password">
            {{ trans('icore::auth.password_confirm') }}
        </label>
        <input 
            id="password-confirm" 
            type="password" 
            class="form-control" 
            name="password_confirmation" 
            required
        >
    </div>
    <div class="form-group">
        <label for="roles">
            {{ trans('icore::users.roles') }}
        </label>
        <select multiple class="form-control" id="roles" name="roles[]">
            @foreach ($roles as $role)
            <option value="{{ $role->name }}">
                {{ $role->name }}
            </option>
            @endforeach
        </select>
    </div>
    <button type="button" class="btn btn-primary store">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.submit') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
