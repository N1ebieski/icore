<form method="post" data-route="{{ route('admin.link.store', [$type]) }}">
    <div class="form-group">
        <label for="name">{{ trans('icore::links.name') }}:</label>
        <input type="text" value="" name="name"
        id="name" class="form-control">
    </div>
    <div class="form-group">
        <label for="url">{{ trans('icore::links.url') }}:</label>
        <input type="text" value="" name="url"
        id="url" class="form-control" placeholder="https://">
    </div>
    <div class="form-group">
        <label for="image">{{ trans('icore::links.img') }}:</label>
        <div class="custom-file" id="image">
            <input type="file" class="custom-file-input" id="img" name="img">
            <label class="custom-file-label" for="img">{{ trans('icore::default.choose_file') }}</label>
        </div>
    </div>
    <div class="form-group">
        <label for="category">
            {{ trans('icore::categories.categories.label') }}:
        </label>
        <div id="category">
            <div id="categoryOptions"></div>
            <div id="searchCategory" data-route="{{ route("admin.category.{$type}.search") }}" data-max=""
            class="position-relative">
                <div class="input-group">
                    <input type="text" class="form-control" id="categories"
                    placeholder="{{ trans('icore::categories.search_categories') }}">
                    <span class="input-group-append">
                        <button class="btn btn-outline-secondary border border-left-0"
                        type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
                <div id="searchCategoryOptions" class="my-3"></div>
            </div>
        </div>
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
