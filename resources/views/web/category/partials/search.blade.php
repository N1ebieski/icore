<div>
@if ($categories->isNotEmpty())
    @foreach ($categories as $category)
        <div class="form-group" id="category{{ $category->id }}">
            <div class="custom-control custom-checkbox">
                <input 
                    type="checkbox" 
                    class="custom-control-input categoryOption"
                    id="categoryOption{{ $category->id }}" 
                    name="categories[{{ $category->id }}]" 
                    value="{{ $category->id }}"
                    {{ ($checked == true) ? 'checked' : '' }}
                >
                <label 
                    class="custom-control-label" 
                    for="categoryOption{{ $category->id }}"
                >
                    @if ($category->ancestors->count() > 0)
                        @foreach ($category->ancestors as $ancestor)
                            {{ $ancestor->name }} &raquo;
                        @endforeach
                    @endif
                    <strong>{{ $category->name }}</strong>
                </label>
            </div>
            @includeWhen($errors->has("categories.{$category->id}"), 'icore::admin.partials.errors', ['name' => "categories.{$category->id}"])
        </div>
    @endforeach
@endif
</div>
