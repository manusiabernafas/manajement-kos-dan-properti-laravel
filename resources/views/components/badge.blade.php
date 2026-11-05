@props(['variant' => 'muted'])

<span {{ $attributes->merge(['class' => 'badge badge-' . $variant]) }}>{{ $slot }}</span>
