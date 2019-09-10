<h5 class="mt-3 mb-2">{{ trans('icore::pages.map') }}:</h5>
@if ($pages->isNotEmpty())
<div class="row">
    @include('icore::web.components.page.footer.partials.chunks', ['pages' => $pages])
    <div class="col-md-3 col-sm-6">
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="{{ route('web.post.index') }}"
                class="@isUrl(route('web.post.index'), 'font-weight-bold')">
                    {{ trans('icore::posts.page.blog') }}
                </a>
            </li>
            <li class="list-group-item">
                <a href="{{ route('web.contact.index') }}"
                class="@isUrl(route('web.contact.index'), 'font-weight-bold')">
                    {{ trans('icore::contact.page.index') }}
                </a>
            </li>
        </ul>
    </div>
</div>
@endif
