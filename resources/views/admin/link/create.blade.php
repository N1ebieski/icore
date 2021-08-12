<form method="post" data-route="{{ route('admin.link.store', [$type]) }}">
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
    @if ($type === 'link')
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
            class="selectpicker select-picker-category" 
            data-live-search="true"
            data-abs="true"
            data-abs-max-options-length="10"
            data-abs-text-attr="name"
            data-abs-ajax-url="{{ route('api.category.index') }}"
            data-style="border"
            data-width="100%"
            multiple
            name="categories[]"
            id="categories"
        >
        </select>
    </div>
    <button type="button" class="btn btn-primary store">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
