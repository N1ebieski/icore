@extends(config('icore.layout') . '::admin.layouts.layout', [
    'title' => [$page->title, trans('icore::pages.route.edit')],
    'desc' => [$page->title, trans('icore::pages.route.edit')],
    'keys' => [$page->title, trans('icore::pages.route.edit')]
])

@section('breadcrumb')
<li class="breadcrumb-item">
    <a 
        href="{{ route('admin.page.index') }}" 
        title="{{ trans('icore::pages.route.index') }}"
    >
        {{ trans('icore::pages.route.index') }}
    </a>
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ trans('icore::pages.route.edit') }}
</li>
@endsection

@section('content')
<div class="w-100">
    <h1 class="h5 mb-4 border-bottom pb-2">
        <i class="fas fa-edit"></i>
        <span> {{ trans('icore::pages.route.edit') }}:<span>
    </h1>
    <form 
        class="mb-3" 
        method="post" 
        action="{{ route('admin.page.update_full', [$page->id]) }}" 
        id="editfull-page"
    >
        @csrf
        @method('put')
        <div class="row">
            <div class="col-lg-9 form-group">
                <div class="form-group">
                    <label for="title">
                        {{ trans('icore::pages.title') }}
                    </label>
                    <input 
                        type="text" 
                        value="{{ old('title', $page->title) }}"
                        name="title" 
                        id="title" 
                        class="form-control {{ $isValid('title') }}"
                    >
                    @includeWhen($errors->has('title'), 'icore::admin.partials.errors', ['name' => 'title'])
                </div>
                <div class="form-group">
                    <label 
                        class="d-flex justify-content-between" 
                        for="content_html_trumbowyg"
                    >
                        <div>
                            {{ trans('icore::pages.content') }}:
                        </div>
                        @include('icore::admin.partials.counter', [
                            'string' => old('content_html', $page->content_html),
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
                        >{{ old('content_html', $page->content_html) }}</textarea>
                        @includeWhen($errors->has('content_html'), 'icore::admin.partials.errors', ['name' => 'content_html'])
                    </div>
                </div>
                <div class="form-group">
                    <label for="tags">
                        <span>{{ trans('icore::pages.tags.label') }} </span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="{{ trans('icore::pages.tags.tooltip', ['max_tags' => config('icore.page.max_tags'), 'max_chars' => config('icore.tag.max_chars')]) }}" 
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <input 
                        name="tags" 
                        id="tags" 
                        class="form-control tagsinput {{ $isValid('tags') }}"
                        value="{{ old('tags', $page->tag_list) }}" 
                        placeholder="{{ trans('icore::pages.tags.placeholder') }}" 
                        data-max="{{ config('icore.page.max_tags') }}"
                        data-max-chars="{{ config('icore.tag.max_chars') }}"
                    >
                    @includeWhen($errors->has('tags'), 'icore::admin.partials.errors', ['name' => 'tags'])
                </div>                
                <hr>
                <div class="form-group">
                    <label for="seo_title">
                        <span>SEO Title</span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="{{ trans('icore::pages.seo.tooltip') }}"
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <input 
                        type="text" 
                        value="{{ old('seo_title', $page->seo_title) }}" 
                        name="seo_title" 
                        id="seo_title"
                        class="form-control {{ $isValid('seo_title') }}" 
                        placeholder="Wpisz SEO title"
                    >
                    @includeWhen($errors->has('seo_title'), 'icore::admin.partials.errors', ['name' => 'seo_title'])
                </div>
                <div class="form-group">
                    <label for="seo_desc">
                        <span>SEO Description</span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="{{ trans('icore::pages.seo.tooltip') }}"
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <textarea 
                        name="seo_desc" 
                        class="form-control {{ $isValid('seo_desc') }}" 
                        rows="3"
                        id="seo_desc"
                    >{{ old('seo_desc', $page->seo_desc) }}</textarea>
                    @includeWhen($errors->has('seo_desc'), 'icore::admin.partials.errors', ['name' => 'seo_desc'])
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="icon">
                        <span>{{ trans('icore::pages.icon.label') }}</span>
                        <i 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="{{ trans('icore::pages.icon.tooltip') }}"
                            class="far fa-question-circle"
                        ></i>
                    </label>
                    <input 
                        type="text" 
                        value="{{ old('icon', $page->icon) }}" 
                        name="icon" 
                        id="icon"
                        class="form-control {{ $isValid('icon') }}" 
                        placeholder="{{ trans('icore::pages.icon.placeholder') }}"
                    >
                    @includeWhen($errors->has('icon'), 'icore::admin.partials.errors', ['name' => 'icon'])
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="seo_noindex" value="{{ Page\SeoNoindex::INACTIVE }}">
                        <input 
                            type="checkbox" 
                            class="custom-control-input" 
                            id="seo_noindex" 
                            name="seo_noindex"
                            value="{{ Page\SeoNoindex::ACTIVE }}" 
                            {{ (old('seo_noindex', $page->seo_noindex->getValue()) == Page\SeoNoindex::ACTIVE) ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="seo_noindex">
                            SEO noindex?
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="seo_nofollow" value="{{ Page\SeoNofollow::INACTIVE }}">
                        <input 
                            type="checkbox" 
                            class="custom-control-input" 
                            id="seo_nofollow" 
                            name="seo_nofollow"
                            value="{{ Page\SeoNofollow::ACTIVE }}" 
                            {{ (old('seo_nofollow', $page->seo_nofollow->getValue()) == Page\SeoNofollow::ACTIVE) ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="seo_nofollow">
                            SEO nofollow?
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="comment" value="{{ Page\Comment::INACTIVE }}">
                        <input 
                            type="checkbox" 
                            class="custom-control-input" 
                            id="comment" 
                            name="comment"
                            value="{{ Page\Comment::ACTIVE }}" 
                            {{ (old('comment', $page->comment->getValue()) == Page\Comment::ACTIVE) ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="comment">
                            {{ trans('icore::pages.comment') }}?
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="status">
                        {{ trans('icore::filter.status.label') }}
                    </label>
                    <select class="custom-select" id="status" name="status">
                        <option 
                            value="{{ Page\Status::ACTIVE }}" 
                            {{ (old('status', $page->status->getValue()) == Page\Status::ACTIVE) ? 'selected' : '' }}
                        >
                            {{ trans('icore::filter.active') }}
                        </option>
                        <option 
                            value="{{ Page\Status::INACTIVE }}" 
                            {{ (old('status', $page->status->getValue()) == Page\Status::INACTIVE) ? 'selected' : '' }}
                        >
                            {{ trans('icore::filter.inactive') }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="user">
                        {{ trans('icore::pages.author') }}:
                    </label>
                    <select 
                        class="selectpicker select-picker" 
                        data-live-search="true"
                        data-abs="true"
                        data-abs-max-options-length="10"
                        data-abs-text-attr="name"
                        data-abs-ajax-url="{{ route('api.user.index') }}"
                        data-style="border"
                        data-width="100%"
                        name="user"
                        id="user"
                    >
                        @if ($userSelection !== null)
                        <optgroup label="{{ trans('icore::default.current_option') }}">
                            <option value="{{ $userSelection->id }}" selected>
                                {{ $userSelection->name }}
                            </option>
                        </optgroup>
                        @endif
                    </select>
                    @includeWhen($errors->has('user'), 'icore::admin.partials.errors', ['name' => 'user'])
                </div>
                @if ($parents->count() > 0)
                <div class="form-group">
                    <label for="parent_id">
                        {{ trans('icore::filter.parent') }}
                    </label>
                    <select 
                        class="selectpicker select-picker" 
                        data-live-search="true"
                        data-style="border"
                        data-width="100%"
                        name="parent_id"
                        id="parent_id"
                    >
                        <option 
                            value="0" 
                            {{ (old('parent_id') == null) ? 'selected' : '' }}
                        >
                            {{ trans('icore::pages.null') }}
                        </option>
                        @foreach ($parents as $parent)
                        <option 
                            @if ($parent->ancestors->isNotEmpty())
                            data-content='<small class="p-0 m-0">{{ implode(' &raquo; ', $parent->ancestors->pluck('title')->toArray()) }} &raquo; </small>{{ $parent->title }}'
                            @endif                        
                            value="{{ $parent->id }}" 
                            {{ (old('parent_id', $page->parent_id) == $parent->id) ? 'selected' : '' }}
                        >
                            {{ $parent->title }}
                        </option>
                        @endforeach
                    </select>
                    @includeWhen($errors->has('parent_id'), 'icore::admin.partials.errors', ['name' => 'parent_id'])
                </div>
                @endif
                <hr>
                <button type="submit" class="btn btn-primary">
                    {{ trans('icore::default.save') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script')
@component('icore::admin.partials.jsvalidation')
{!! JsValidator::formRequest('N1ebieski\ICore\Http\Requests\Admin\Page\UpdateRequest', '#editfull-page'); !!}
@endcomponent
@endpush
