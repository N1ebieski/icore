<form 
    data-route="{{ route('admin.link.update_position', [$link->id]) }}"
    data-id="{{ $link->id }}" 
    id="update"
>
    @if ((int)$siblings_count > 0)
    <div class="form-group">
        <label for="position">
            {{ trans('icore::default.position') }}
        </label>
        <select class="form-control custom-select" id="position" name="position">
        @for ($i=0; $i<$siblings_count; $i++)
            <option 
                value="{{ $i }}" 
                {{ (old('position', $link->position) === $i) ? 'selected' : '' }}
            >
                {{ $i+1 }}
            </option>
        @endfor
        </select>
    </div>
    <button type="button" class="btn btn-primary updatePositionPage">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    @endif
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
