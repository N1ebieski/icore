@if ($pages->isNotEmpty())
<ul id="pagesToggle" class="navbar-nav pr-3 pr-md-1">
    @foreach ($pages as $page)
    <li class="nav-item dropdown {{ $isUrlContains($page->urls ?? null) }}">
        @if (empty($page->content_html))
        <a 
            href="#" 
            class="nav-link" 
            role="button" 
            id="navbarDropdownMenu{{ $page->id }}"
            data-toggle="dropdown" 
            aria-haspopup="true" 
            aria-expanded="false"
        >
        @else
        <a 
            href="{{ route('web.page.show', $page->slug) }}" 
            title="{{ $page->title }}"
            class="nav-link {{ $isUrl(route('web.page.show', $page->slug)) }}"
        >
        @endif
            @if (!empty($page->icon))
            <i class="{{ $page->icon }}"></i>
            @endif
            <span class="d-md-inline d-none">{{ $page->short_title }}</span>
            <span class="d-md-none d-inline">{{ $page->title }}</span>
        </a>
        @if (empty($page->content_html) && $page->childrens->isNotEmpty())
        <div 
            class="dropdown-menu dropdown-menu-right {{ $loop->last ? 'mb-3' : null }}" 
            aria-labelledby="navbarDropdownMenu{{ $page->id }}"
        >
            @foreach ($page->childrens as $children)
            <a 
                class="dropdown-item {{ $isUrl(route('web.page.show', $children->slug)) }}"
                href="{{ route('web.page.show', $children->slug) }}"
            >
                @if (!empty($children->icon))
                <i class="fa-fw {{ $children->icon }}"></i>
                @endif
                <span>{{ $children->title }}</span>
            </a>
            @endforeach
        </div>
        @endif
    </li>
    @endforeach
</ul>
@endif
