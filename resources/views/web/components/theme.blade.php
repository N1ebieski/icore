<a 
    class="nav-link text-nowrap" 
    href="#" 
    role="button" 
    id="dropdown-theme"
    data-toggle="dropdown" 
    aria-haspopup="true" 
    aria-expanded="false"
>
    <span class="fas fa-lg fa-{{ $themes[$currentTheme] }}"></span>
    <span class="d-inline d-md-none">{{ trans('icore::default.' . $currentTheme) }}</span>
</a>
<div 
    class="dropdown-menu dropdown-menu-right"
    id="theme-dropdown-toggle"
    aria-labelledby="dropdown-theme"
>
    <h6 class="dropdown-header">
        {{ trans('icore::default.theme_toggle') }}:
    </h6>
    @foreach ($themes as $theme => $icon)
    <a 
        class="dropdown-item {{ $isCurrentTheme($theme) }}"
        data-theme="{{ $theme }}"
        href="#{{ $theme }}" 
        title="{{ trans('icore::default.' . $theme) }}"
    >
        <span class="fas fa-{{ $icon }}"></span>
        <span>{{ trans('icore::default.' . $theme) }}</span>
    </a>
    @endforeach                       
</div>
