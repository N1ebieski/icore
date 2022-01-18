<form data-route="{{ route('admin.category.'.$model->poli.'.index') }}" id="filter">
    <div class="d-flex flex-wrap position-relative">
        <div class="mb-3 mr-auto">
            <span class="badge badge-primary">
                {{ trans('icore::filter.items') }}: {{ $categories->total() }}
            </span>
            @if ($filter['search'] !== null)
            <span>
                <a 
                    href="#" 
                    class="badge badge-primary filter-option" 
                    data-name="filter[search]"
                >
                    <span>{{ trans('icore::filter.search.label') }}: {{ $filter['search'] }}</span>
                    <span aria-hidden="true">&times;</span>
                </a>
            </span>
            @endif
            @if ($filter['status'] !== null)
            <span>
                <a 
                    href="#" 
                    class="badge badge-primary filter-option" 
                    data-name="filter[status]"
                >
                    <span>{{ trans('icore::filter.status.label') }}: {{ trans("icore::filter.status.{$filter['status']}") }}</span>
                    <span aria-hidden="true">&times;</span>
                </a>
            </span>
            @endif
            @if ($filter['parent'] !== null)
            <span>
                <a 
                    href="#" 
                    class="badge badge-primary filter-option" 
                    data-name="filter[parent]"
                >
                    <span>{{ trans('icore::filter.parent') }}: {{ $filter['parent']->name ?? trans('icore::categories.roots') }}</span>
                    <span aria-hidden="true">&times;</span>
                </a>
            </span>
            @endif
            @if (array_filter($filter))
            <span>
                <a 
                    href="{{ route('admin.category.'.$model->poli.'.index') }}" 
                    class="badge badge-dark"
                >
                    {{ trans('icore::default.clear') }}
                </a>
            </span>
            @endif
        </div>
        <div class="ml-sm-auto">
            <div class="form-inline d-flex flex-nowrap">
                <div class="form-group col-xs-4">
                    <button 
                        class="btn border mr-2" 
                        href="#" 
                        type="button" 
                        data-toggle="modal"
                        data-target="#filter-modal"
                    >
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
                <div class="form-group col-xs-4 mr-2">
                    <label class="sr-only" for="filter-orderby">
                        {{ trans('icore::filter.order') }}
                    </label>
                    <select 
                        class="form-control custom-select filter" 
                        name="filter[orderby]" 
                        id="filter-orderby"
                    >
                        <option value="">
                            {{ trans('icore::filter.order') }} {{ trans('icore::filter.default') }}
                        </option>
                        <option 
                            value="created_at|desc"
                            {{ ($filter['orderby'] == 'created_at|desc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.created_at')) }} {{ trans('icore::filter.desc') }}
                        </option>
                        <option 
                            value="created_at|asc"
                            {{ ($filter['orderby'] == 'created_at|asc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.created_at')) }} {{ trans('icore::filter.asc') }}
                        </option>
                        <option 
                            value="updated_at|desc"
                            {{ ($filter['orderby'] == 'updated_at|desc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.updated_at')) }} {{ trans('icore::filter.desc') }}
                        </option>
                        <option 
                            value="updated_at|asc"
                            {{ ($filter['orderby'] == 'updated_at|asc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.updated_at')) }} {{ trans('icore::filter.asc') }}
                        </option>
                        <option 
                            value="name|desc"
                            {{ ($filter['orderby'] == 'name|desc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.name')) }} {{ trans('icore::filter.desc') }}
                        </option>
                        <option 
                            value="name|asc"
                            {{ ($filter['orderby'] == 'name|asc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.name')) }} {{ trans('icore::filter.asc') }}
                        </option>
                        <option 
                            value="position|desc"
                            {{ ($filter['orderby'] == 'position|desc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.position')) }} {{ trans('icore::filter.desc') }}
                        </option>
                        <option 
                            value="position|asc"
                            {{ ($filter['orderby'] == 'position|asc') ? 'selected' : '' }}
                        >
                            {{ mb_strtolower(trans('icore::filter.position')) }} {{ trans('icore::filter.asc') }}
                        </option>
                    </select>
                </div>
                <div class="form-group col-xs-4">
                    <label class="sr-only" for="filter-paginate">
                        {{ trans('icore::filter.paginate') }}
                    </label>
                    <select 
                        class="form-control custom-select filter" 
                        name="filter[paginate]" 
                        id="filter-paginate"
                    >
                        <option 
                            value="{{ $paginate }}" 
                            {{ ($filter['paginate'] == $paginate) ? 'selected' : '' }}
                        >
                            {{ $paginate }}
                        </option>
                        <option 
                            value="{{ ($paginate*2) }}" 
                            {{ ($filter['paginate'] == ($paginate*2)) ? 'selected' : '' }}
                        >
                            {{ ($paginate*2) }}
                        </option>
                        <option 
                            value="{{ ($paginate*4) }}" 
                            {{ ($filter['paginate'] == ($paginate*4)) ? 'selected' : '' }}
                        >
                            {{ ($paginate*4) }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    @include('icore::admin.category.partials.filter_filter')
</form>
