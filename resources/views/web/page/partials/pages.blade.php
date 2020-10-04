@foreach ($pages as $page)
    <div class="list-group-item d-flex justify-content-between align-items-center">
        @if (!empty($page->content))
        <a 
            href="{{ route('web.page.show', $page->slug) }}" 
            title="{{ $page->title }}"
            class="{{ $isUrl(route('web.page.show', $page->slug), 'font-weight-bold') }}"
        >
        @endif
            <span>{{ str_repeat('-', $page->real_depth) }}</span>
            @if (!empty($page->icon))
            <i class="{{ $page->icon }} text-center" style="width:1.5rem"></i>
            @endif
            <span>{{ $page->title }}</span>
        @if (!empty($page->content)) 
        </a>
        @endif
    </div>
    @if ($page->relationLoaded('childrensRecursiveWithAllRels'))
        @include('icore::web.page.partials.pages', [
            'pages' => $page->childrensRecursiveWithAllRels
        ])
    @endif
@endforeach
