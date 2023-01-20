<a 
    class="nav-link text-nowrap" 
    href="#" 
    role="button" 
    id="dropdown-multi-lang"
    data-toggle="dropdown" 
    aria-haspopup="true" 
    aria-expanded="false"
>
    <span class="fi fil-{{ $currentLang }} pb-1"></span>
    <span class="d-md-none d-lg-inline">{{ mb_strtoupper($currentLang) }}</span>
</a>
<div 
    class="dropdown-menu dropdown-menu-right" 
    id="dropdown-multi-lang-toggle"
    aria-labelledby="dropdown-multi-lang"
>
    <h6 class="dropdown-header">
        {{ trans('icore::default.lang_toggle') }}:
    </h6>
    @foreach ($langs as $lang)
    <a 
        class="dropdown-item {{ $isCurrentLang($lang) }}" 
        data-lang="{{ $lang }}"
        href="{{ $getCurrentUrlWithLang($lang) }}" 
        title="{{ $lang }}"
    >
        <span class="fi fil-{{ $lang }}"></span>
        <span>{{ mb_strtoupper($lang) }}</span>
    </a>
    @endforeach                       
</div>
