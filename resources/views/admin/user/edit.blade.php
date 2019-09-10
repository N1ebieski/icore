<form data-route="{{ route('admin.user.update', ['user' => $user->id]) }}"
data-id="{{ $user->id }}" id="update">
    <div class="form-group">
        <label for="name">{{ trans('icore::auth.name') }}</label>
        <input type="text" value="{{ $user->name }}" name="name"
        class="form-control" id="name">
    </div>
    <div class="form-group">
        <label for="email">{{ trans('icore::auth.address') }}</label>
        <input type="email" value="{{ $user->email }}" name="email"
        class="form-control" id="email">
    </div>
    <div class="form-group">
        <label for="roles">{{ trans('icore::user.roles') }}</label>
        <select multiple class="form-control" id="roles" name="roles[]">
            @foreach ($roles as $role)
            <option value="{{ $role->name }}"
                @foreach ($user->roles as $user_role)
                {{ ($role->id === $user_role->id) ? 'selected' : '' }}
                @endforeach
            >{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="button" data-id="{{ $user->id }}" class="btn btn-primary update">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.save') }}
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
    @if (!$user->socialites->isEmpty())
        <hr>
        {{ ucfirst(trans('icore::users.symlink')) }}: <span class="text-primary">
        @foreach ($user->socialites as $user_socialite)
            <i class="fab fa-{{ $user_socialite->provider_name }}"></i> {{ $user_socialite->provider_name }}@if (!$loop->last),@endif
        @endforeach
        </span>
    @endif
</form>
