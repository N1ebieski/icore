@foreach ($pages as $page)
    @if ($maxDepth === null || $page->real_depth <= $maxDepth)
    <div class="list-group-item">
        @if (!empty($page->content))
        <a 
            href="{{ route('web.page.show', $page->slug) }}" 
            title="{{ $page->title }}"
            class="{{ $isUrl(route('web.page.show', $page->slug), 'font-weight-bold') }}"
        >
            <span>{{ str_repeat('-', $page->real_depth) }}</span>
            @if (!empty($page->icon))
            <i class="{{ $page->icon }}"></i>
            @endif
            <span>{{ $page->title }}</span>
        </a>
        @else
            {{ str_repeat('-', $page->real_depth) }} {{ $page->title }}
        @endif
    </div>
    @if ($page->relationLoaded('childrensRecursiveWithAllRels'))
        @include('icore::web.components.page.footer.partials.pages', [
            'pages' => $page->childrensRecursiveWithAllRels
        ])
    @endif
    @endif
@endforeach
