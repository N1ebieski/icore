<h3 class="h5">{{ trans('icore::pages.pages') }}</h3>
<div class="list-group list-group-flush mb-3">
    @if ($page->relationLoaded('ancestors'))
        @include('icore::web.page.partials.pages', ['pages' => $page->ancestors])
    @endif
    @foreach ($page->siblings as $sibling)   
    <div class="list-group-item d-flex justify-content-between align-items-center">
        <a href="{{ route('web.page.show', $sibling->slug) }}"
        class="@isUrl(route('web.page.show', $sibling->slug), 'font-weight-bold')">
            {{ str_repeat('-', $sibling->real_depth) }} {{ $sibling->title }}
        </a>
    </div>
    @if ($sibling->id === $page->id && $page->relationLoaded('childrensRecursiveWithAllRels'))
        @include('icore::web.page.partials.pages', ['pages' => $page->childrensRecursiveWithAllRels])
    @endif
    @endforeach
</div>
