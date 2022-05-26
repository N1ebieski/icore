@if (session()->has('success'))
<div 
    class="toast bg-success" 
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
    @if (session()->has('message'))
    <div class="toast-body bg-light text-dark">
        {{ session()->get('message') }}
    </div>
    @endif
</div>
@endif
