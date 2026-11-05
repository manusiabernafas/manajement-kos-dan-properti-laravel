@props(['title' => null])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || isset($actions))
        <div class="card-header">
            @if($title)<h3>{{ $title }}</h3>@endif
            @isset($actions)<div>{{ $actions }}</div>@endisset
        </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
    @isset($footer)
        <div class="card-footer">{{ $footer }}</div>
    @endisset
</div>
