@foreach ($categories as $category)
    <div class="list-group-item d-flex justify-content-between align-items-center">
        <a href="{{ route('web.category.post.show', $category->slug) }}"
        class="@isUrl(route('web.category.post.show', $category->slug), 'font-weight-bold')">
            {{ str_repeat('-', $category->real_depth) }} @if (!empty($category->icon))<i class="{{ $category->icon }}"></i>&nbsp;@endif{{ $category->name }}
        </a>
        <span class="badge badge-primary badge-pill">{{ $category->posts_count }}</span>
    </div>
    @if ($category->relationLoaded('childrensRecursiveWithAllRels'))
        @include('icore::web.components.category.partials.categories', ['categories' => $category->childrensRecursiveWithAllRels])
    @endif
@endforeach
