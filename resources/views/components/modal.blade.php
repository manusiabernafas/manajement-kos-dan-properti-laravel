@props(['id', 'title', 'size' => ''])

<div class="modal-overlay" id="{{ $id }}">
    <div class="modal {{ $size ? 'modal-' . $size : '' }}">
        <div class="modal-header">
            <h3>{{ $title }}</h3>
            <button type="button" class="modal-close" data-modal-close><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            {{ $slot }}
        </div>
        @isset($footer)
            <div class="modal-footer">{{ $footer }}</div>
        @endisset
    </div>
</div>
