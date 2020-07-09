<nav class="mb-3">
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-single" 
        role="tab" aria-controls="nav-single" aria-selected="true">{{ trans('icore::default.single') }}</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-json" 
        role="tab" aria-controls="nav-json" aria-selected="false">{{ trans('icore::default.global') }}</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-single" role="tabpanel" aria-labelledby="nav-single-tab">
        <form data-route="{{ route('admin.category.'.$model->poli.'.store') }}" id="store">
            <div class="form-group">
                <label for="name">{{ trans('icore::categories.name') }}</label>
                <input type="text" value="" name="name" class="form-control" id="name">
            </div>
            <div class="form-group">
                <label for="icon">
                    {{ trans('icore::categories.icon.label') }} <i data-toggle="tooltip" data-placement="top"
                    title="{{ trans('icore::categories.icon.tooltip') }}" class="far fa-question-circle"></i>
                </label>
                <input type="text" value="{{ old('icon') }}" name="icon" id="icon"
                class="form-control {{ $isValid('icon') }}" placeholder="{{ trans('icore::categories.icon.placeholder') }}">
            </div>
            @if ($categories->count() > 0)
            <div class="form-group">
                <label for="parent_id">{{ trans('icore::categories.parent_id') }}</label>
                <select class="form-control" id="parent_id" name="parent_id">
                    <option value="0" {{ $parent_id === 0 ? 'selected' : '' }}>
                        {{ trans('icore::categories.null') }}
                    </option>
                    @foreach ($categories as $cats)
                        @if ($cats->real_depth == 0)
                            <optgroup label="----------"></optgroup>
                        @endif
                    <option value="{{ $cats->id }}" {{ $parent_id === $cats->id ? 'selected' : '' }}>
                        {{ str_repeat('-', $cats->real_depth) }} {{ $cats->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            <button type="button" class="btn btn-primary store">
                <i class="fas fa-check"></i>
                <span>{{ trans('icore::default.submit') }}</span>
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fas fa-ban"></i>
                <span>{{ trans('icore::default.cancel') }}</span>
            </button>
        </form>
    </div>
    <div class="tab-pane fade" id="nav-json" role="tabpanel" aria-labelledby="nav-json-tab">
        <form data-route="{{ route('admin.category.'.$model->poli.'.store_global') }}" id="store">
            <div class="form-group">
                <label for="names">{{ trans('icore::categories.names_json') }}</label>
                <textarea name="names" class="form-control" rows="10" id="names"></textarea>
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="clear" name="clear" value="1"
                    data-target="#collapseParentId" data-toggle="collapse">
                    <label class="custom-control-label" for="clear">{{ trans('icore::categories.clear') }}</label>
                </div>
            </div>
            <div class="collapse show" id="collapseParentId">
                @if ($categories->count() > 0)
                <div class="form-group">
                    <label for="parent_id">{{ trans('icore::categories.parent_id') }}</label>
                    <select class="form-control" id="parent_id" name="parent_id">
                        <option value="0" {{ $parent_id === 0 ? 'selected' : '' }}>
                            {{ trans('icore::categories.null') }}
                        </option>
                        @foreach ($categories as $cats)
                        @if ($cats->real_depth == 0)
                        <optgroup label="----------"></optgroup>
                        @endif
                        <option value="{{ $cats->id }}" {{ $parent_id === $cats->id ? 'selected' : '' }}>
                            {{ str_repeat('-', $cats->real_depth) }} {{ $cats->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            <button type="button" class="btn btn-primary store">
                <i class="fas fa-check"></i>
                <span>{{ trans('icore::default.submit') }}</span>
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fas fa-ban"></i>
                <span>{{ trans('icore::default.cancel') }}</span>
            </button>
        </form>
    </div>
</div>
