@component('icore::admin.partials.modal')

@slot('modal_id', 'edit-position-modal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('icore::pages.route.edit_position') }}</span>
@endslot

@slot('modal_body')
<form 
    id="edit-position"
    data-route="{{ route('admin.page.update_position', [$page->id]) }}"
    data-id="{{ $page->id }}" 
>
    @if ((int)$page->siblings_count > 0)
    <div class="form-group">
        <label for="position">
            {{ trans('icore::pages.position') }}:
        </label>
        <select class="form-control custom-select" id="position" name="position">
        @for ($i=0; $i<$page->siblings_count; $i++)
            <option 
                value="{{ $i }}" 
                {{ (old('position', $page->position) == $i) ? 'selected' : '' }}
            >
                {{ $i+1 }}
            </option>
        @endfor
        </select>
    </div>
    @endif
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary update-position"
        form="edit-position"
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