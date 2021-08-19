@component('icore::web.partials.modal')
@slot('modal_id', 'edit-profile-email-modal')

@slot('modal_title')
{{ trans('icore::profile.change_email') }}
@endslot

@slot('modal_body')
<form 
    method="post" 
    action="{{ route('web.profile.update_email') }}" 
    id="update-email-profile"
>
    @csrf
    @method('patch')
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
            placeholder="{{ trans('icore::auth.address.placeholder') }}"
        >
    </div>
    <div class="form-group">
        <label for="password">
            {{ trans('icore::auth.password_confirm') }}
        </label>
        <input 
            id="password_confirmation" 
            class="form-control" 
            name="password_confirmation" 
            oninput="this.setAttribute('type', 'password');" 
            required
        >
    </div>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.submit') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
@endslot
@endcomponent
