<form 
    data-route="{{ route('admin.page.update_position', [$page->id]) }}"
    data-id="{{ $page->id }}" 
    id="update"
>
    @if ((int)$page->siblings_count > 0)
    <div class="form-group">
        <label for="position">
            {{ trans('icore::pages.position') }}
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
    <button type="button" class="btn btn-primary updatePositionPage">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
