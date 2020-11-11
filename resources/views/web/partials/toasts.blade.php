@if (session()->has('success'))
<div 
    class="toast bg-success text-white" 
    role="alert" 
    aria-live="assertive" 
    aria-atomic="true" 
    data-delay="20000" 
>
    <div class="toast-header">
        <strong class="mr-auto">{{ session()->get('success') }}</strong>
        <button 
            type="button" 
            class="text-dark ml-2 mb-1 close" 
            data-dismiss="toast" 
            aria-label="{{ trans('icore::default.close') }}"
        >
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
@endif
