@props(['variant' => 'info', 'icon' => null])

@php
    $icons = [
        'success' => 'fa-solid fa-circle-check',
        'warning' => 'fa-solid fa-triangle-exclamation',
        'danger' => 'fa-solid fa-circle-xmark',
        'info' => 'fa-solid fa-circle-info',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'alert alert-' . $variant]) }}>
    <i class="{{ $icon ?? $icons[$variant] ?? $icons['info'] }}"></i>
    <div>{{ $slot }}</div>
</div>
