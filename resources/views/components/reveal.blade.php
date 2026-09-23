@props(['type' => 'up', 'delay' => 0, 'as' => 'div'])

@php
    $typeClass = match ($type) {
        'down' => 'reveal-down',
        'left' => 'reveal-left',
        'right' => 'reveal-right',
        'zoom' => 'reveal-zoom',
        'fade' => 'reveal-fade',
        default => 'reveal-up',
    };
@endphp

<{{ $as }}
    @if ((int) $delay > 0) style="transition-delay: {{ (int) $delay }}ms" @endif
    {{ $attributes->merge(['class' => 'reveal '.$typeClass]) }}
>
    {{ $slot }}
</{{ $as }}>