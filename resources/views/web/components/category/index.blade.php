<h3 class="h5">{{ trans('icore::categories.categories') }}</h3>
@if ($categories->isNotEmpty())
<div class="list-group list-group-flush mb-3">
    @include('icore::web.components.category.partials.categories', ['categories' => $categories])
</div>
@endif
