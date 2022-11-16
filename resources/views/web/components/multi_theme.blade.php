<a 
    class="nav-link text-nowrap" 
    href="#" 
    role="button" 
    id="dropdown-multi-theme"
    data-toggle="dropdown" 
    aria-haspopup="true" 
    aria-expanded="false"
>
    <span class="fas fa-lg fa-icore-{{ $currentTheme }}"></span>
    <span class="d-inline d-md-none">{{ trans('icore::default.' . $currentTheme) }}</span>
</a>
<div 
    class="dropdown-menu dropdown-menu-right"
    id="dropdown-multi-theme-toggle"
    aria-labelledby="dropdown-multi-theme"
>
    <h6 class="dropdown-header">
        {{ trans('icore::default.theme_toggle') }}:
    </h6>
    @foreach ($themes as $theme)
    <a 
        class="dropdown-item {{ $isCurrentTheme($theme) }}"
        data-theme="{{ $theme }}"
        href="#{{ $theme }}" 
        title="{{ trans('icore::default.' . $theme) }}"
    >
        <span class="fas fa-icore-{{ $theme }}"></span>
        <span>{{ trans('icore::default.' . $theme) }}</span>
    </a>
    @endforeach                       
</div>
