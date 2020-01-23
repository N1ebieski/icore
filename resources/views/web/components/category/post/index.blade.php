@if ($categories->isNotEmpty())
<h3 class="h5">{{ trans('icore::categories.categories') }}</h3>
<div class="list-group list-group-flush mb-3">
    @include('icore::web.components.category.post.partials.categories', ['categories' => $categories])
</div>
@endif
