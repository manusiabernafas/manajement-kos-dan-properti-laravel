@props(['icon' => 'fa-solid fa-inbox', 'title', 'description' => null])

<div class="empty-state">
    <div class="empty-state-icon"><i class="{{ $icon }}"></i></div>
    <h3>{{ $title }}</h3>
    @if($description)
        <p>{{ $description }}</p>
    @endif
    @isset($action)
        {{ $action }}
    @endisset
</div>
