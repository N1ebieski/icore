<a 
    class="nav-link text-nowrap" 
    href="#" 
    role="button" 
    id="dropdown-multi-lang"
    data-toggle="dropdown" 
    aria-haspopup="true" 
    aria-expanded="false"
>
    <span class="fi fil-{{ $currentLang }}"></span>
    <span class="d-md-none d-lg-inline">{{ mb_strtoupper($currentLang) }}</span>
</a>
<div 
    class="dropdown-menu dropdown-menu-right" 
    aria-labelledby="dropdown-multi-lang"
>
    @foreach ($langs as $lang)
    <a 
        class="dropdown-item {{ $isLang($lang) }}" 
        href="{{ $getCurrentUrlWithLang($lang) }}" 
        title="{{ $lang }}"
    >
        <span class="fi fil-{{ $lang }}"></span>
        <span>{{ mb_strtoupper($lang) }}</span>
    </a>
    @endforeach                       
</div>
