<form data-route="{{ url()->current() }}" id="filter">
    <div class="d-flex position-relative">
        <div class="form-group ml-auto">
            <label class="sr-only" for="filterCommentOrderBy">
                {{ trans('icore::filter.order') }}
            </label>
            <select 
                class="form-control custom-select filter" 
                name="filter[orderby]" 
                id="filterCommentOrderBy"
            >
                <option value="">
                    {{ trans('icore::filter.order') }} {{ trans('icore::filter.default') }}
                </option>
                <option 
                    value="created_at|desc"
                    {{ ($filter['orderby'] == 'created_at|desc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.created_at')) }}
                    {{ trans('icore::filter.desc') }}
                </option>
                <option 
                    value="created_at|asc"
                    {{ ($filter['orderby'] == 'created_at|asc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.created_at')) }}
                    {{ trans('icore::filter.asc') }}
                </option>
                <option 
                    value="sum_rating|desc"
                    {{ ($filter['orderby'] == 'sum_rating|desc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.sum_rating')) }}
                    {{ trans('icore::filter.desc') }}
                </option>
                <option 
                    value="sum_rating|asc"
                    {{ ($filter['orderby'] == 'sum_rating|asc') ? 'selected' : '' }}
                >
                    {{ mb_strtolower(trans('icore::filter.sum_rating')) }}
                    {{ trans('icore::filter.asc') }}
                </option>
            </select>
        </div>
    </div>
</form>
