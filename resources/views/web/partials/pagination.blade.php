<div class="d-flex flex-wrap" id="is-pagination">
    <div class="text-left mr-auto mt-3">
    @if ($items->currentPage() < $items->lastPage())
        @if (($next ?? false) === true)
        <a 
            href="{{ $items->appends(request()->input())->nextPageUrl() }}" 
            rel="nofollow" 
            id="is-next" 
            role="button" 
            title="{{ trans('icore::pagination.next_page') }}"
            class="btn btn-outline-secondary text-nowrap"
        >
            <span>{{ trans('icore::pagination.next_page') }}</span>
            <i class="fas fa-angle-down"></i>
        </a>
        @else
        <a 
            href="{{ url()->full() }}" 
            rel="nofollow" 
            id="is-next" 
            role="button" 
            title="{{ trans('icore::pagination.next_items', ['paginate' => ($filter['paginate'] ?? config('database.paginate'))]) }}"
            class="btn btn-outline-secondary text-nowrap"
        >
            <span>{{ trans('icore::pagination.next_items', ['paginate' => ($filter['paginate'] ?? config('database.paginate'))]) }}</span>
            <i class="fas fa-angle-down"></i>
        </a>
        @endif
    @endif
    </div>
    <div class="pagination-sm ml-auto mt-3">
        {{ $items->appends(request()->query())->fragment($fragment ?? '')->links() }}
    </div>
</div>
