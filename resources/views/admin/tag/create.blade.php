<form method="post" data-route="{{ route('admin.tag.store') }}">
    <div class="form-group">
        <label for="name">
            {{ trans('icore::tags.name') }}:
        </label>
        <input 
            type="text" 
            value="" 
            name="name"
            id="name" 
            class="form-control"
        >
    </div>
    <button type="button" class="btn btn-primary store">
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.save') }}</span>
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-ban"></i>
        <span>{{ trans('icore::default.cancel') }}</span>
    </button>
</form>
