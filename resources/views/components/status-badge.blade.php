@props(['status', 'colors' => [], 'labels' => []])

@php
    $variant = $colors[$status] ?? 'muted';
    $label = $labels[$status] ?? ucwords(str_replace('_', ' ', $status));
@endphp

<span class="badge badge-{{ $variant }}">{{ $label }}</span>
