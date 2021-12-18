@component('icore::admin.partials.modal')

@slot('modal_id', 'edit-position-modal')

@slot('modal_title')
<i class="fas fa-sort-amount-up"></i>
<span> {{ trans('icore::categories.route.edit_position') }}</span>
@endslot

@slot('modal_body')
<form 
    data-route="{{ route('admin.category.update_position', [$category->id]) }}"
    data-id="{{ $category->id }}" 
    id="edit-position-category"
>
    @if ((int)$category->siblings_count > 0)
    <div class="form-group">
        <label for="position">
            {{ trans('icore::categories.position') }}
        </label>
        <select class="form-control custom-select" id="position" name="position">
        @for ($i=0; $i<$category->siblings_count; $i++)
            <option 
                value="{{ $i }}" 
                {{ (old('position', $category->position) == $i) ? 'selected' : '' }}
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
        class="btn btn-primary update-position-category"
        form="edit-position-category"
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
