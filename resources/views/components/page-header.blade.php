@props(['title', 'description' => null])

<div class="page-header">
    <div>
        <h1>{{ $title }}</h1>
        @if($description)
            <p>{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="page-header-actions">{{ $actions }}</div>
    @endisset
</div>
