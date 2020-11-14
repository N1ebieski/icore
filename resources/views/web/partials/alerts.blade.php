@if (session()->has('success'))
<div class="alert alert-success alert-time" role="alert">
    {{ session()->get('success') }}
    <button 
        type="button" 
        class="text-dark close" 
        data-dismiss="alert" 
        aria-label="{{ trans('icore::default.close') }}"
    >
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session()->has('warning'))
<div class="alert alert-warning alert-time" role="alert">
    {{ session()->get('warning') }}
    <button 
        type="button" 
        class="text-dark close" 
        data-dismiss="alert" 
        aria-label="{{ trans('icore::default.close') }}"
    >
        <span aria-hidden="true">&times;</span>
    </button>    
</div>
@endif

@if (session()->has('danger'))
<div class="alert alert-danger alert-time" role="alert">
    {{ session()->get('danger') }}
    <button 
        type="button" 
        class="text-dark close" 
        data-dismiss="alert" 
        aria-label="{{ trans('icore::default.close') }}"
    >
        <span aria-hidden="true">&times;</span>
    </button>    
</div>
@endif

@if (session()->has('alertErrors') && $errors->any())
<div class="alert alert-danger alert-time" role="alert">
    <div class="d-flex justify-content-between">
        <ul class="list-unstyled mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button 
            type="button" 
            class="text-dark close mb-auto" 
            data-dismiss="alert" 
            aria-label="{{ trans('icore::default.close') }}"
        >
            <span aria-hidden="true">&times;</span>
        </button>
    </div>    
</div>
@endif
