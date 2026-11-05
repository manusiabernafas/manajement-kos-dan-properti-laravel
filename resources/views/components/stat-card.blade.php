@props(['label', 'value', 'icon' => 'fa-solid fa-chart-simple', 'variant' => 'primary', 'meta' => null, 'metaDirection' => null])

<div class="stat-card">
    <div class="stat-card-top">
        <span class="stat-card-label">{{ $label }}</span>
        <span class="stat-card-icon badge-{{ $variant }}" style="background: var(--color-{{ $variant }}-soft); color: var(--color-{{ $variant }});">
            <i class="{{ $icon }}"></i>
        </span>
    </div>
    <div class="stat-card-value">{{ $value }}</div>
    @if($meta)
        <div class="stat-card-meta {{ $metaDirection }}">
            @if($metaDirection === 'up')<i class="fa-solid fa-arrow-trend-up"></i>@elseif($metaDirection === 'down')<i class="fa-solid fa-arrow-trend-down"></i>@endif
            {{ $meta }}
        </div>
    @endif
</div>
