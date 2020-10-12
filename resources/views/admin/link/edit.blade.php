<form 
    method="post" 
    data-route="{{ route('admin.link.update', [$link->id]) }}" 
    data-id="{{ $link->id }}"
>
    <div class="form-group">
        <label for="name">
            {{ trans('icore::links.name') }}:
        </label>
        <input
            type="text" 
            value="{{ $link->name }}" 
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
            value="{{ $link->url }}" 
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
    @if ($link->img_url !== null)
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input 
                type="checkbox" 
                class="custom-control-input" 
                id="delete_img" 
                name="delete_img"
            >
            <label class="custom-control-label" for="delete_img">
                {{ trans('icore::links.delete_img') }}
            </label>
        </div>
    </div>
    @endif
    <div class="form-group">
        <label for="category">
            {{ trans('icore::categories.categories.label') }}:
        </label>
        <div id="category">
            <div id="categoryOptions">
                @include('icore::web.category.partials.search', [
                    'categories' => $link->categories, 
                    'checked' => true
                ])
            </div>
            <div 
                id="searchCategory" 
                data-route="{{ route("admin.category.{$link->type}.search") }}" 
                data-max=""
                class="position-relative"
            >
                <div class="input-group">
                    <input 
                        type="text" 
                        class="form-control" 
                        id="categories"
                        placeholder="{{ trans('icore::categories.search_categories') }}"
                    >
                    <span class="input-group-append">
                        <button 
                            class="btn btn-outline-secondary border border-left-0"
                            type="button"
                        >
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
                <div id="searchCategoryOptions" class="my-3"></div>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary update">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
