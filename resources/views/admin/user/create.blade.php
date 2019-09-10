<form data-route="{{ route('admin.user.store') }}" id="store">
    <div class="form-group">
        <label for="name">{{ trans('icore::auth.name') }}</label>
        <input type="text" value="{{ old('name') }}" name="name"
        class="form-control" id="name" placeholder="{{ trans('icore::auth.enter_name') }}">
    </div>
    <div class="form-group">
        <label for="email">{{ trans('icore::auth.address') }}</label>
        <input type="email" value="{{ old('email') }}" name="email"
        class="form-control" id="email" placeholder="{{ trans('icore::auth.enter_address') }}">
    </div>
    <div class="form-group">
        <label for="password">{{ trans('icore::auth.password') }}</label>
        <input id="password" type="password" class="form-control" name="password" required>
    </div>
    <div class="form-group">
        <label for="password">{{ trans('icore::auth.password_confirm') }}</label>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
    </div>
    <div class="form-group">
        <label for="roles">{{ trans('icore::user.roles') }}</label>
        <select multiple class="form-control" id="roles" name="roles[]">
            @foreach ($roles as $role)
            <option value="{{ $role->name }}">{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="button" class="btn btn-primary store">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.submit') }}
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</form>
