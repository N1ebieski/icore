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
    @if (count(config('icore.multi_langs')) > 1)
    <div class="form-group">
        <label for="lang">
            <span>{{ trans('icore::multi_langs.lang') }} / {{ trans('icore::multi_langs.progress.label') }}:</span>
            <i 
                data-toggle="tooltip" 
                data-placement="top" 
                title="{{ trans('icore::multi_langs.progress.tooltip') }}"
                class="far fa-question-circle"
            ></i>            
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <select 
                    class="selectpicker select-picker edit-lang" 
                    data-style="border"
                    data-width="100%"
                    data-target="#edit-modal"
                    data-lang="{{ config('app.locale') }}"
                    id="lang"
                >
                    @foreach (config('icore.multi_langs') as $lang)
                    <option
                        data-content='<span class="fi fil-{{ $lang }}"></span> <span>{{ mb_strtoupper($lang) }}</span>'
                        value="{{ route('admin.category.edit', ['lang' => $lang, 'category' => $category->id]) }}"
                        {{ config('app.locale') === $lang ? 'selected' : '' }}
                    >
                        {{ $lang }}
                    </option>
                    @endforeach
                </select>
            </div>
            <input
                type="number"
                min="0"
                max="100"
                step="1"
                class="form-control"
                id="progress"
                name="progress"
                value="{{ $category->current_lang->progress }}"
            >
            <div class="input-group-append">
                <span class="input-group-text">%</span>
            </div>            
        </div>
    </div>
    <div class="form-group">
        <div class="custom-control custom-switch">
            <input type="hidden" name="auto_translate" value="{{ AutoTranslate::INACTIVE }}">
            <input 
                type="checkbox" 
                class="custom-control-input" 
                id="auto_translate" 
                name="auto_translate"
                value="{{ AutoTranslate::ACTIVE }}" 
                {{ $category->auto_translate->isActive() ? 'checked' : '' }}
            >
            <label class="custom-control-label" for="auto_translate">
                {{ trans('icore::multi_langs.auto_trans') }}?
            </label>
        </div>
    </div>    
    @endif
    <div class="form-group">
        <label for="name">
            {{ trans('icore::categories.name') }}:
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
            <span>{{ trans('icore::categories.icon.label') }}:</span> 
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
            {{ trans('icore::categories.parent_id') }}:
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
            data-lang="{{ config('app.locale') }}"
            data-style="border"
            data-width="100%"
            data-container="body"
        >
            <optgroup label="{{ trans('icore::default.current_option') }}">
                <option value="" {{ ($category->isRoot()) ? 'selected' : '' }}>
                    {{ trans('icore::categories.null') }}
                </option>
                @if ($category->parent !== null)
                <option 
                    @if ($category->parent->ancestors->isNotEmpty())
                    data-content='<small class="p-0 m-0">{{ implode(' &raquo; ', $category->parent->ancestors->pluck('name')->map(fn ($item) => $item ?? trans('icore::multi_langs.no_trans'))->toArray()) }} &raquo; </small>{{ $category->parent->name ?? trans('icore::multi_langs.no_trans') }}'
                    @endif
                    value="{{ $category->parent->id }}" 
                    selected
                >
                    {{ $category->parent->name ?? trans('icore::multi_langs.no_trans') }}
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