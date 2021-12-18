@component('icore::admin.partials.modal')

@slot('modal_id', 'edit-modal')

@slot('modal_size', 'modal-lg')

@slot('modal_title')
<i class="far fa-edit"></i>
<span> {{ trans('icore::posts.route.edit') }}</span>
@endslot

@slot('modal_body')
<form 
    id="edit-post"
    data-route="{{ route('admin.post.update', ['post' => $post->id]) }}"
    data-id="{{ $post->id }}" 
>
    <div class="form-group">
        <label for="title">
            {{ trans('icore::posts.title') }}
        </label>
        <input 
            type="text" 
            value="{{ $post->title }}" 
            name="title"
            class="form-control" 
            id="title"
        >
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
                contenteditable="true" 
                spellcheck="true" 
                name="content_html" 
                id="content_html_trumbowyg" 
                class="form-control" 
                data-lang="{{ config('app.locale') }}"
                rows="10" 
                id="content_html"
            >{{ old('content_html', $post->content_html) }}</textarea>
        </div>
    </div>
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary update"
        form="edit-post"
    >
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button 
        type="button" 
        class="btn btn-secondary" 
        data-dismiss="modal"
    >
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</div>
@endslot

@endcomponent