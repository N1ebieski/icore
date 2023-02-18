<a 
    class="nav-link text-nowrap" 
    href="#" 
    role="button" 
    id="dropdown-theme-toggle"
    data-toggle="dropdown" 
    aria-haspopup="true" 
    aria-expanded="false"
>
    <i class="fa-solid fa-sun"></i>
</a>
<div 
    class="dropdown-menu dropdown-menu-right" 
    id="theme-toggle" 
    aria-labelledby="dropdown-theme-toggle"
    aria-label="{{ trans('icore::default.theme_toggle') }}"
>
    @foreach ($themes as $theme)
    <a 
        class="dropdown-item {{ $isTheme($lang) }}" 
        href="{{ $getCurrentUrlWithLang($lang) }}" 
        title="{{ $lang }}"
    >
        <span class="fi fil-{{ $lang }}"></span>
        <span>{{ mb_strtoupper($lang) }}</span>
    </a>
    @endforeach                       
</div>


<div 
    class="btn-group my-auto" 
    id="theme-toggle" 
    role="group" 
    aria-label="{{ trans('icore::default.theme_toggle') }}"
>
    <button 
        type="button" 
        class="btn btn-sm btn-light border" 
        style="width:80px;"
        {{ $isTheme(['', null], 'disabled') }}
    >
        {{ trans('icore::default.light') }}
    </button>
    <button 
        type="button" 
        class="btn btn-sm btn-dark border" 
        style="width:80px;"
        {{ $isTheme('dark', 'disabled') }}
    >
        {{ trans('icore::default.dark') }}
    </button>
</div>
