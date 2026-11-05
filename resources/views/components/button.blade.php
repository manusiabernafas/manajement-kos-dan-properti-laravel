@props(['variant' => 'primary', 'size' => null, 'type' => 'button', 'loading' => false, 'icon' => null])

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'btn btn-' . $variant . ($size ? ' btn-' . $size : '')]) }}
>
    @if($loading)
        <span class="spinner"></span>
    @endif
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    <span class="btn-label">{{ $slot }}</span>
</button>
