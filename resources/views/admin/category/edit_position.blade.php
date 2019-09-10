<form data-route="{{ route('admin.category.update_position', [$category->id]) }}"
data-id="{{ $category->id }}" id="update">
    @if ((int)$category->siblings_count > 0)
    <div class="form-group">
        <label for="position">{{ trans('icore::categories.position') }}</label>
        <select class="form-control" id="position" name="position">
        @for ($i=0; $i<$category->siblings_count; $i++)
            <option value="{{ $i }}" {{ (old('position', $category->position) == $i) ? 'selected' : '' }}>
                {{ $i+1 }}
            </option>
        @endfor
        </select>
    </div>
    @endif
    <button type="button" class="btn btn-primary updatePositionPage">
        <i class="fas fa-check"></i>
        {{ trans('icore::default.save') }}
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        {{ trans('icore::default.cancel') }}
    </button>
</form>
