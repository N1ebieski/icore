@component('icore::admin.partials.modal')

@slot('modal_id', 'edit-modal')

@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::categories.route.edit') }}</span>
@endslot

@slot('modal_body')
<form 
    data-route="{{ route('admin.category.update', ['category' => $category->id]) }}"
    data-id="{{ $category->id }}" 
    id="edit-category"
>
    <div class="form-group">
        <label for="name">
            {{ trans('icore::categories.name') }}
        </label>
        <input 
            type="text" value="{{ $category->name }}" 
            name="name"
            class="form-control" 
            id="name"
        >
    </div>
    <div class="form-group">
        <label for="icon">
            <span>{{ trans('icore::categories.icon.label') }}</span> 
            <i 
                data-toggle="tooltip" 
                data-placement="top" 
                title="{{ trans('icore::categories.icon.tooltip') }}"
                class="far fa-question-circle"
            ></i>
        </label>
        <input 
            type="text" 
            value="{{ old('icon', $category->icon) }}" 
            name="icon" 
            id="icon"
            class="form-control {{ $isValid('icon') }}" 
            placeholder="{{ trans('icore::categories.icon.placeholder') }}"
        >
    </div>
    <div class="form-group">
        <label for="parent_id">
            {{ trans('icore::categories.parent_id') }}
        </label>
        <select 
            id="parent_id"            
            name="parent_id"    
            class="selectpicker select-picker-category" 
            data-live-search="true"
            data-abs="true"
            data-abs-max-options-length="10"
            data-abs-text-attr="name"
            data-abs-ajax-url="{{ route("api.category.{$category->poli}.index") }}"
            data-abs-default-options="{{ json_encode([['value' => '', 'text' => trans('icore::categories.null')]]) }}"
            data-abs-filter-except="{{ json_encode($category->descendants->pluck('id')->toArray()) }}"
            data-style="border"
            data-width="100%"
            data-size="5"
        >
            <optgroup label="{{ trans('icore::default.current_option') }}">
                <option value="" {{ ($category->isRoot()) ? 'selected' : '' }}>
                    {{ trans('icore::categories.null') }}
                </option>
                @if ($category->parent !== null)
                <option 
                    @if ($category->parent->ancestors->isNotEmpty())
                    data-content='<small class="p-0 m-0">{{ implode(' &raquo; ', $category->parent->ancestors->pluck('name')->toArray()) }} &raquo; </small>{{ $category->parent->name }}'
                    @endif
                    value="{{ $category->parent->id }}" 
                    selected
                >
                    {{ $category->parent->name }}
                </option>
                @endif
            </optgroup>
        </select>
    </div>
</form>
@endslot

@slot('modal_footer')    
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary update"
        form="edit-category"
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