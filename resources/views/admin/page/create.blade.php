@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [trans('icore::pages.route.create')],
    'desc' => [trans('icore::pages.route.create')],
    'keys' => [trans('icore::pages.route.create')]
])

@inject('page', 'N1ebieski\ICore\Models\Page\Page')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('admin.page.index') }}" title="{{ trans('icore::pages.route.index') }}">
        {{ trans('icore::pages.route.index') }}
    </a>
</li>
<li class="breadcrumb-item active" aria-current="page">{{ trans('icore::pages.route.create') }}</li>
@endsection

@section('content')
<div class="w-100">
    <h1 class="h5 mb-4 border-bottom pb-2">
        <i class="far fa-plus-square"></i>
        <span>{{ trans('icore::pages.route.create') }}:</span>
    </h1>
    <form class="mb-3" method="post" action="{{ route('admin.page.store') }}" id="createPage">
        @csrf
        <div class="row">
            <div class="col-lg-9 form-group">
                <div class="form-group">
                    <label for="title">{{ trans('icore::pages.title') }}</label>
                    <input type="text" value="{{ old('title') }}" name="title" id="title" class="form-control {{ $isValid('title') }}">
                    @includeWhen($errors->has('title'), 'icore::admin.partials.errors', ['name' => 'title'])
                </div>
                <div class="form-group">
                    <label class="d-flex justify-content-between" for="content_html_trumbowyg">
                        <div>{{ trans('icore::pages.content') }}:</div>
                        @include('icore::admin.partials.counter', [
                            'string' => old('content_html'),
                            'name' => 'content_html'
                        ])
                    </label>                    
                    <div class="{{ $isTheme('dark', 'trumbowyg-dark') }}">
                        <textarea name="content_html" id="content_html_trumbowyg" class="form-control {{ $isValid('content_html') }}"
                        rows="10" id="content_html">{{ old('content_html') }}</textarea>
                        @includeWhen($errors->has('content_html'), 'icore::admin.partials.errors', ['name' => 'content_html'])
                    </div>
                </div>
                <div class="form-group">
                    <label for="tags">
                        <span>{{ trans('icore::pages.tags.label') }} </span>
                        <i data-toggle="tooltip" data-placement="top" 
                        title="{{ trans('icore::pages.tags.tooltip', ['max_tags' => config('icore.page.max_tags')]) }}"
                        class="far fa-question-circle"></i>
                    </label>
                    <input name="tags" id="tags" class="form-control tagsinput {{ $isValid('tags') }}"
                    value="{{ old('tags') }}" placeholder="Dodaj tag" data-max="{{ config('icore.page.max_tags') }}">
                    @includeWhen($errors->has('tags'), 'icore::admin.partials.errors', ['name' => 'tags'])
                </div>
                <hr>
                <div class="form-group">
                    <label for="seo_title">
                        SEO Title <i data-toggle="tooltip" data-placement="top" title="{{ trans('icore::pages.seo.tooltip') }}"
                        class="far fa-question-circle"></i>
                    </label>
                    <input type="text" value="{{ old('seo_title') }}" name="seo_title" id="seo_title"
                    class="form-control {{ $isValid('seo_title') }}" placeholder="Wpisz SEO title">
                    @includeWhen($errors->has('seo_title'), 'icore::admin.partials.errors', ['name' => 'seo_title'])
                </div>
                <div class="form-group">
                    <label for="seo_desc">
                        SEO Description <i data-toggle="tooltip" data-placement="top" title="{{ trans('icore::pages.seo.tooltip') }}"
                        class="far fa-question-circle"></i>
                    </label>
                    <textarea name="seo_desc" class="form-control {{ $isValid('seo_desc') }}" rows="3"
                    id="seo_desc">{{ old('seo_desc') }}</textarea>
                    @includeWhen($errors->has('seo_desc'), 'icore::admin.partials.errors', ['name' => 'seo_desc'])
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="icon">
                        {{ trans('icore::pages.icon.label') }} <i data-toggle="tooltip" data-placement="top" title="{{ trans('icore::pages.icon.tooltip') }}"
                        class="far fa-question-circle"></i>
                    </label>
                    <input type="text" value="{{ old('icon') }}" name="icon" id="icon"
                    class="form-control {{ $isValid('icon') }}" placeholder="{{ trans('icore::pages.icon.placeholder') }}">
                    @includeWhen($errors->has('icon'), 'icore::admin.partials.errors', ['name' => 'icon'])
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="seo_noindex" name="seo_noindex"
                        value="1" {{ (old('seo_noindex') == $page::SEO_NOINDEX) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="seo_noindex">SEO noindex?</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="seo_nofollow" name="seo_nofollow"
                        value="1" {{ (old('seo_nofollow') == $page::SEO_NOFOLLOW) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="seo_nofollow">SEO nofollow?</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="comment" name="comment"
                        value="1" {{ (old('comment') == $page::WITH_COMMENT) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="comment">{{ trans('icore::pages.comment') }}?</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="status">{{ trans('icore::filter.status.label') }}</label>
                    <select class="custom-select" id="status" name="status">
                        <option value="{{ $page::ACTIVE }}" {{ (old('status') == $page::ACTIVE) ? 'selected' : '' }}>
                            {{ trans('icore::filter.active') }}
                        </option>
                        <option value="{{ $page::INACTIVE }}" {{ (old('status') == $page::INACTIVE) ? 'selected' : '' }}>
                            {{ trans('icore::filter.inactive') }}
                        </option>
                    </select>
                </div>
                @if ($parents->count() > 0)
                <div class="form-group">
                    <label for="parent_id">{{ trans('icore::filter.parent') }}</label>
                    <select class="form-control custom-select" id="parent_id" name="parent_id">
                        <option value="0" {{ (old('parent_id') == null) ? 'selected' : '' }}>
                            {{ trans('icore::pages.null') }}
                        </option>
                        @foreach ($parents as $parent)
                        @if ($parent->real_depth == 0)
                        <optgroup label="----------"></optgroup>
                        @endif
                        <option value="{{ $parent->id }}" {{ (old('parent_id') == $parent->id) ? 'selected' : '' }}>
                            {{ str_repeat('-', $parent->real_depth) }} {{ $parent->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <hr>
                <button type="submit" class="btn btn-primary">{{ trans('icore::default.submit') }}</button>
            </div>
        </div>
    </form>
</div>
@endsection
