@component('icore::web.partials.modal')
@slot('modal_id', 'editProfileEmailModal')

@slot('modal_title')
{{ trans('icore::profile.change_email') }}
@endslot

@slot('modal_body')
<form method="post" action="{{ route('web.profile.update_email') }}" id="updateEmailProfile">
    @csrf
    @method('patch')
    <div class="form-group">
        <label for="email">{{ trans('icore::auth.address') }}</label>
        <input type="email" value="{{ $user->email }}" name="email"
        class="form-control" id="email" placeholder="{{ trans('icore::auth.enter_address') }}">
    </div>
    <div class="form-group">
        <label for="password">{{ trans('icore::auth.password_confirm') }}</label>
        <input id="password_confirmation" class="form-control" name="password_confirmation" oninput="this.setAttribute('type', 'password');" required>
    </div>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.submit') }}
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</form>
@endslot
@endcomponent
