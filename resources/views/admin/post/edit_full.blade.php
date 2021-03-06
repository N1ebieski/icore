@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [$post->title, trans('icore::posts.route.edit')],
    'desc' => [$post->title, trans('icore::posts.route.edit')],
    'keys' => [$post->title, trans('icore::posts.route.edit')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a 
        href="{{ route('admin.post.index') }}" 
        title="{{ trans('icore::posts.route.index') }}"
    >
        {{ trans('icore::posts.route.index') }}
    </a>
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('icore::posts.route.edit') }}
</li>
@endsection

@section('content')
<div class="w-100">
    <h1 class="h5 mb-4 border-bottom pb-2">
        <i class="fas fa-edit"></i>
        <span>{{ trans('icore::posts.route.edit') }}:</span>
    </h1>
    <form 
        class="mb-3" 
        method="post" 
        action="{{ route('admin.post.update_full', ['post' => $post->id]) }}" 
        id="editFullPost"
    >
        @csrf
        @method('put')
        <div class="row">
            <div class="col-lg-9 form-group">
                <div class="form-group">
                    <label for="title">
                        {{ trans('icore::posts.title') }}
                    </label>
                    <input 
                        type="text" 
                        value="{{ old('title', $post->title) }}" 
                        name="title" 
                        id="title" 
                        class="form-control {{ $isValid('title') }}"
                        placeholder="Wpisz tytuł posta"
                    >
                    @includeWhen($errors->has('title'), 'icore::admin.partials.errors', ['name' => 'title'])
                </div>
                <div class="form-group">
                    <label class="d-flex justify-content-between" for="content_html_trumbowyg">
                        <div>
                            {{ trans('icore::posts.content') }}:
                        </div>
                        @include('icore::admin.partials.counter', [
                            'string' => old('content_html', $post->content_html),
                            'name' => 'content_html'
                        ])
                    </label>                    
                    <div class="{{ $isTheme('dark', 'trumbowyg-dark') }}">
                        <textarea 
                            name="content_html" 
                            id="content_html_trumbowyg" 
                            class="form-control {{ $isValid('content_html') }}"
                            rows="10" 
                            id="content_html" 
                            data-lang="{{ config('app.locale') }}"
                        >{{ old('content_html', $post->content_html) }}</textarea>
                    </div>
                    @includeWhen($errors->has('content_html'), 'icore::admin.partials.errors', ['name' => 'content_html'])
                </div>
                <div class="form-group">
                    <label for="tags">
                        <span>{{ trans('icore::posts.tags.label') }}</span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="{{ trans('icore::posts.tags.tooltip', ['max_tags' => $maxTags, 'max_chars' => config('icore.tag.max_chars')]) }}" 
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <input 
                        name="tags" 
                        id="tags" 
                        class="form-control tagsinput {{ $isValid('tags') }}"
                        value="{{ old('tags', $post->tagList) }}" 
                        placeholder="{{ trans('icore::posts.tags.placeholder') }}" 
                        data-max="{{ $maxTags }}"
                        data-max-chars="{{ config('icore.tag.max_chars') }}"
                    >
                    @includeWhen($errors->has('tags'), 'icore::admin.partials.errors', ['name' => 'tags'])
                </div>
                <hr>
                <div class="form-group">
                    <label for="seo_title">
                        <span>SEO title</span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="{{ trans('icore::posts.seo.tooltip') }}" 
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <input 
                        type="text" 
                        value="{{ old('seo_title', $post->seo_title) }}" 
                        name="seo_title" 
                        id="seo_title"
                        class="form-control {{ $isValid('seo_title') }}"
                        placeholder="Wpisz SEO title"
                    >
                    @includeWhen($errors->has('seo_title'), 'icore::admin.partials.errors', ['name' => 'seo_title'])
                </div>
                <div class="form-group">
                    <label for="seo_desc">
                        <span>SEO description</span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="{{ trans('icore::posts.seo.tooltip') }}" 
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <textarea 
                        name="seo_desc" 
                        class="form-control {{ $isValid('seo_desc') }}"
                        rows="3" 
                        id="seo_desc"
                    >{{ old('seo_desc', $post->seo_desc) }}</textarea>
                    @includeWhen($errors->has('seo_desc'), 'icore::admin.partials.errors', ['name' => 'seo_desc'])
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="seo_noindex" value="0">
                        <input 
                            type="checkbox" 
                            class="custom-control-input" 
                            id="seo_noindex" 
                            name="seo_noindex" 
                            value="{{ $post::SEO_NOINDEX }}" 
                            {{ (old('seo_noindex', $post->seo_noindex) == $post::SEO_NOINDEX) ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="seo_noindex">
                            SEO noindex?
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="seo_nofollow" value="0">
                        <input 
                            type="checkbox" 
                            class="custom-control-input" 
                            id="seo_nofollow" 
                            name="seo_nofollow" 
                            value="{{ $post::SEO_NOFOLLOW }}" 
                            {{ (old('seo_nofollow', $post->seo_nofollow) == $post::SEO_NOFOLLOW) ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="seo_nofollow">
                            SEO nofollow?
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="comment" value="0">
                        <input 
                            type="checkbox" 
                            class="custom-control-input" 
                            id="comment" 
                            name="comment"
                            value="1" 
                            {{ (old('comment', $post->comment) == $post::WITH_COMMENT) ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="comment">
                            {{ trans('icore::posts.comment') }}?
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="status">
                        {{ trans('icore::filter.status.label') }}
                    </label>
                    <select 
                        class="custom-select" 
                        data-toggle="collapse" 
                        aria-expanded="false" 
                        aria-controls="collapsePublishedAt" 
                        id="status" 
                        name="status"
                    >
                        <option 
                            value="{{ $post::ACTIVE }}" 
                            {{ (old('status', $post->status) == $post::ACTIVE) ? 'selected' : '' }}
                        >
                            {{ trans('icore::filter.active') }}
                        </option>
                        <option 
                            value="{{ $post::INACTIVE }}" 
                            {{ (old('status', $post->status) == $post::INACTIVE) ? 'selected' : '' }}
                        >
                            {{ trans('icore::filter.inactive') }}
                        </option>
                        <option 
                            value="{{ $post::SCHEDULED }}" 
                            {{ (old('status', $post->status) == $post::SCHEDULED) ? 'selected' : '' }}
                        >
                            {{ trans('icore::filter.scheduled') }}
                        </option>
                    </select>
                </div>
                <div 
                    class="form-group collapse {{ (old('status', $post->status) != $post::INACTIVE) ? 'show' : '' }}" 
                    id="collapsePublishedAt"
                >
                    <label for="published_at">
                        <span>{{ trans('icore::posts.published_at.label') }}</span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="{{ trans('icore::posts.published_at.tooltip') }}" 
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <div id="published_at">
                        <div class="form-group">
                            <input 
                                type="text" 
                                data-value="{{ now()->parse(old('date_published_at', $post->published_at))->format('Y/m/d') }}"
                                value="" 
                                name="date_published_at" 
                                id="date_published_at" 
                                class="form-control datepicker"
                                data-lang="{{ config('app.locale') }}"
                            >
                            @includeWhen($errors->has('date_published_at'), 'icore::admin.partials.errors', ['name' => 'date_published_at'])
                        </div>
                        <div class="form-group">
                            <input 
                                type="text" 
                                data-value="{{ now()->parse(old('time_published_at', $post->published_at))->format('H:i') }}"
                                value="" 
                                name="time_published_at" 
                                id="time_published_at" 
                                class="form-control timepicker"
                                data-lang="{{ config('app.locale') }}"
                            >
                            @includeWhen($errors->has('time_published_at'), 'icore::admin.partials.errors', ['name' => 'time_published_at'])
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="category">
                        <span>{{ trans('icore::categories.categories.label') }}</span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="{{ trans('icore::categories.categories.tooltip', ['max_categories' => $maxCategories]) }}" 
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <div id="category">
                        <div id="categoryOptions">
                            @include('icore::admin.category.partials.search', [
                                'categories' => $categoriesSelection, 
                                'checked' => true
                            ])
                        </div>
                        <div 
                            id="searchCategory" {{ $categoriesSelection->count() >= $maxCategories ? 'style=display:none' : '' }}
                            data-route="{{ route('admin.category.post.search') }}" 
                            data-max="{{ $maxCategories }}"
                            class="position-relative"
                        >
                            <div class="input-group">
                                <input 
                                    type="text" 
                                    class="form-control {{ $isValid('category') }}" 
                                    placeholder="{{ trans('icore::categories.search_categories') }}"
                                >
                                <span class="input-group-append">
                                    <button 
                                        class="btn btn-outline-secondary border border-left-0"
                                        type="button"
                                    >
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                            <div id="searchCategoryOptions" class="my-3"></div>
                        </div>
                    </div>
                    @includeWhen($errors->has('categories'), 'icore::admin.partials.errors', ['name' => 'categories'])
                </div>
                <hr>
                <button type="submit" class="btn btn-primary">
                    {{ trans('icore::default.save') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
