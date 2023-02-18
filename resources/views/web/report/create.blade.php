@component('icore::web.partials.modal')

@slot('modal_id', 'create-report-modal')

@slot('modal_title')
<i class="fas fa-exclamation-triangle"></i>
<span>{{ trans('icore::reports.route.create') }}</span>
@endslot

@slot('modal_body')
<form 
    id="create-report"
    method="post"  
    data-id="{{ $model->id }}"
    data-route="{{ route("web.report.{$model->poli_self}.store", [$model->id]) }}"
>
    <div class="form-group">
        <label for="content">
            {{ trans('icore::reports.reason') }}:
        </label>
        <input type="text" value="" name="content" class="form-control" id="content">    
    </div>
    <x-icore::captcha-component    
        id="1000"
    />        
</form>
@endslot

@slot('modal_footer')
<div class="d-inline">
    <button 
        type="button" 
        class="btn btn-primary store-report"
        form="create-report"
    >
        <i class="fas fa-check"></i>
        <span>{{ trans('icore::default.submit') }}</span>
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
