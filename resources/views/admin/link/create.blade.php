@component('icore::admin.partials.modal')

@slot('modal_id', 'create-modal')

@slot('modal_title')
<i class="far fa-plus-square"></i>
<span> {{ trans('icore::links.route.create') }}</span>
@endslot

@slot('modal_body')
<form 
    id="create-link"
    method="post" 
    data-route="{{ route('admin.link.store', [$type]) }}"
>
    <div class="form-group">
        <label for="name">
            {{ trans('icore::links.name') }}:
        </label>
        <input 
            type="text" 
            value="" 
            name="name"
            id="name" 
            class="form-control"
        >
    </div>
    <div class="form-group">
        <label for="url">
            {{ trans('icore::links.url') }}:
        </label>
        <input 
            type="text" 
            value="" 
            name="url"
            id="url" 
            class="form-control" 
            placeholder="https://"
        >
    </div>
    <div class="form-group">
        <label for="image">
            {{ trans('icore::links.img') }}:
        </label>
        <div class="custom-file" id="image">
            <input type="file" class="custom-file-input" id="img" name="img">
            <label class="custom-file-label" for="img">
                {{ trans('icore::default.choose_file') }}
            </label>
        </div>
    </div>
    @if ($type === Link\Type::LINK)
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input 
                type="checkbox" 
                class="custom-control-input" 
                id="home" 
                name="home"
                value="1"
            >
            <label class="custom-control-label" for="home">
                {{ trans('icore::links.only.home') }}
            </label>
        </div>
    </div>
    @endif
    <div class="form-group">
        <label for="category">
            {{ trans('icore::links.only.categories') }}:
        </label>
        <input type="hidden" name="categories" value="">
        <select 
            id="categories"   
            name="categories[]"     
            class="selectpicker select-picker-category" 
            data-live-search="true"
            data-abs="true"
            data-abs-max-options-length="10"
            data-abs-text-attr="name"
            data-abs-ajax-url="{{ route('api.category.index') }}"
            data-style="border"
            data-width="100%"
            data-container="body"
            data-lang="{{ config('app.locale') }}"
            multiple
        >
        </select>
    </div>
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary store"
        form="create-link"
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