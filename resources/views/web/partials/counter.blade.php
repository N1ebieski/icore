<small 
    class="counter" 
    data-min="{{ $min ?? null }}" 
    data-max="{{ $max ?? null }}" 
    data-name="{{ $name }}"
>
    [
        @if (($length = mb_strlen(strip_tags($string))) === 0)
        <span>
        @else
        <span class="{{ (isset($min) && $length < $min) || (isset($max) && $length > $max) ? 'text-danger' : 'text-success' }}">
        @endif
            {{ $length }}
        </span>
        @if (isset($max))
        <span>
            / {{ $max }}
        </span>
        @endif
    ]
</small>