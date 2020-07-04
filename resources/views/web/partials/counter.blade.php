<small class="counter" data-min="{{ $min ?? null }}" 
data-max="{{ $max ?? null }}" data-name="{{ $name }}">
    [
        @if (($length = mb_strlen(strip_tags($string))) === 0)
        <span>
        @else
        @php
            if (isset($min) && $length < $min) $text = 'text-danger';
            if (isset($max) && $length > $max) $text = 'text-danger';
        @endphp
        <span class="{{ $text ?? 'text-success' }}">
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