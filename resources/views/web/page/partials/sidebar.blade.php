<h3 class="h5">
    {{ trans('icore::pages.pages') }}
</h3>
<div class="list-group list-group-flush mb-3">
    @if ($page->relationLoaded('ancestors'))
        @include('icore::web.page.partials.pages', ['pages' => $page->ancestors])
    @endif
    @foreach ($page->siblings as $sibling)
    <div class="list-group-item d-flex justify-content-between align-items-center">
        @php
            $siblingWithContent = $getFirstSiblingWithContent($sibling) ?? $sibling;
        @endphp
        @if (!empty($siblingWithContent->content))
        <a 
            href="{{ route('web.page.show', $siblingWithContent->slug) }}" 
            title="{{ $sibling->title }}"
            class="{{ $isUrl(route('web.page.show', $sibling->slug), 'font-weight-bold') }}"
        >
        @endif
            @if ($page->real_depth > 0)
            <span>{{ str_repeat('-', $sibling->real_depth) }}</span>
            @endif
            @if (!empty($sibling->icon))
            <i class="{{ $sibling->icon }} text-center" style="width:1.5rem"></i>
            @endif
            <span>{{ $sibling->title }}</span>
        @if (!empty($siblingWithContent->content)) 
        </a>
        @endif    
    </div>
    @if ($sibling->id === $page->id && $page->relationLoaded('childrensRecursiveWithAllRels'))
        @include('icore::web.page.partials.pages', [
            'pages' => $page->childrensRecursiveWithAllRels
        ])
    @endif
    @endforeach
</div>
