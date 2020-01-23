<div class="row" id="is-pagination" data-title="{{ trans('icore::pagination.page', ['num' => $items->currentPage()]) }}">
    <div class="col text-left mt-3">
        @if ($items->currentPage() < $items->lastPage())
        @if (($next ??= false) === true)
        <a href="{{ $items->appends(request()->input())->nextPageUrl() }}" rel="nofollow" id="is-next" role="button"
        class="btn btn-outline-secondary text-nowrap">
            {{ trans('icore::pagination.next_page') }}
            <i class="fas fa-angle-down"></i>
        </a>
        @else
        <a href="{{ url()->full() }}" rel="nofollow" id="is-next" role="button"
        class="btn btn-outline-secondary text-nowrap">
            {{ trans('icore::pagination.next_items', ['paginate' => ($filter['paginate'] ?? config('database.paginate'))]) }}
            <i class="fas fa-angle-down"></i>
        </a>
        @endif
        @endif
    </div>
    <div class="col-auto pagination-sm mt-3">
        {{ $items->appends(request()->query())->fragment($fragment ?? '')->links() }}
    </div>
</div>
