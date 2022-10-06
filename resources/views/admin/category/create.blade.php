@component('icore::admin.partials.modal')

@slot('modal_id', 'create-modal')

@slot('modal_size', 'modal-lg')

@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('icore::categories.route.create') }}</span>
@endslot

@slot('modal_body')
<nav class="mb-3">
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a 
            class="nav-item nav-link active" 
            id="nav-home-tab" 
            data-toggle="tab" 
            href="#nav-single" 
            role="tab" 
            aria-controls="nav-single" 
            aria-selected="true"
        >
            {{ trans('icore::default.single') }}
        </a>
        <a 
            class="nav-item nav-link" 
            id="nav-profile-tab" 
            data-toggle="tab" 
            href="#nav-json" 
            role="tab" 
            aria-controls="nav-json" 
            aria-selected="false"
        >
            {{ trans('icore::default.global') }}
        </a>
    </div>
</nav>
<div class="tab-content" id="nav-tab-content">
    <div 
        class="tab-pane fade show active" 
        id="nav-single" 
        role="tabpanel" 
        aria-labelledby="nav-single-tab"
    >
        <form 
            id="create-category"        
            data-route="{{ route("admin.category.{$category->poli}.store") }}" 
        >
            <div class="form-group">
                <label for="name">
                    {{ trans('icore::categories.name') }}
                </label>
                <input type="text" value="" name="name" class="form-control" id="name">
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
                    value="{{ old('icon') }}" 
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
                    data-style="border"
                    data-width="100%"
                    data-container="body"
                >
                    <optgroup label="{{ trans('icore::default.current_option') }}">
                        @if ($parent === null)
                        <option 
                            value="" 
                            selected
                        >
                            {{ trans('icore::categories.null') }}
                        </option>
                        @else
                        <option 
                            @if ($parent->ancestors->isNotEmpty())
                            data-content='<small class="p-0 m-0">{{ implode(' &raquo; ', $parent->ancestors->pluck('name')->toArray()) }} &raquo; </small>{{ $parent->name }}'
                            @endif
                            value="{{ $parent->id }}" 
                            selected
                        >
                            {{ $parent->name }}
                        </option>
                        @endif
                    </optgroup>
                </select>
            </div>
        </form>
    </div>
    <div 
        class="tab-pane fade" 
        id="nav-json" 
        role="tabpanel" 
        aria-labelledby="nav-json-tab"
    >
        <form 
            id="create-category"        
            data-route="{{ route("admin.category.{$category->poli}.store_global") }}" 
        >
            <div class="form-group">
                <label for="names">
                    <span>{{ trans('icore::categories.names_json.label') }}</span>
                    <i 
                        data-toggle="tooltip" 
                        data-placement="top"
                        title="{{ trans('icore::categories.names_json.tooltip') }}" 
                        class="far fa-question-circle"
                    ></i>                    
                </label>
                <textarea 
                    name="names" 
                    class="form-control" 
                    rows="10" 
                    id="names"
                    data-autogrow="false"
                ></textarea>
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input 
                        type="checkbox" 
                        class="custom-control-input" 
                        id="clear" 
                        name="clear" 
                        value="1"
                        data-target="#collapse-parent-id" 
                        data-toggle="collapse"
                    >
                    <label class="custom-control-label" for="clear">
                        {{ trans('icore::categories.clear') }}
                    </label>
                </div>
            </div>
            <div class="collapse show" id="collapse-parent-id">
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
                        data-style="border"
                        data-width="100%"
                        data-container="body"
                    >
                        @if ($parent === null)
                        <option 
                            value="" 
                            selected
                        >
                            {{ trans('icore::categories.null') }}
                        </option>
                        @else
                        <option 
                            @if ($parent->ancestors->isNotEmpty())
                            data-content='<small class="p-0 m-0">{{ implode(' &raquo; ', $parent->ancestors->pluck('name')->toArray()) }} &raquo; </small>{{ $parent->name }}'
                            @endif
                            value="{{ $parent->id }}" 
                            selected
                        >
                            {{ $parent->name }}
                        </option>
                        @endif
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary store"
        form="create-category"
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