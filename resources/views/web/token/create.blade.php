@component('icore::admin.partials.modal')

@slot('modal_id', 'create-modal')

@slot('modal_size', 'modal-lg')

@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('icore::tokens.route.create') }}</span>
@endslot

@slot('modal_body')
<form
    id="create-token"
    data-route="{{ route('web.token.store') }}"
>
    <div class="form-group">
        <label for="name">
            {{ trans('icore::tokens.name') }}:
        </label>
        <input
            type="text"
            value=""
            name="name"
            class="form-control"
            id="name"
        >
    </div>
    <div class="form-group">
        <label for="expiration">
            {{ trans('icore::tokens.expiration.label') }}:
        </label>
        <select
            class="form-control custom-select"
            id="expiration"
            name="expiration"
        >
            <option value="">
                {{ trans('icore::tokens.unlimited') }}
            </option>
            @foreach([7, 30, 60, 180, 365] as $days)
            <option value="{{ $days }}">
                {{ $days }} {{ trans('icore::tokens.expiration.days') }}
            </option>
            @endforeach
            <option value="custom">
                {{ trans('icore::tokens.expiration.custom') }}
            </option>
        </select>
    </div>
    <div class="form-group">
        <label for="abilities">
            {{ trans('icore::tokens.abilities') }}:
        </label>
        <div
            id="abilities"
            class="row"
        >
            @foreach ($abilities->chunk($col_count) as $chunk)
            <div class="col-lg-4 col-sm-6">
                @foreach ($chunk as $key => $value)
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="abilities-{{ $key }}"
                            name="abilities[]"
                            value="{{ $value }}"
                            {{ $value === 'api.*' ? 'checked' : null }}
                        >
                        <label
                            class="custom-control-label"
                            for="abilities-{{ $key }}"
                        >
                            {{ $value }}
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button
        type="button"
        class="btn btn-primary store"
        form="create-token"
    >
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.submit') }}</span>
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