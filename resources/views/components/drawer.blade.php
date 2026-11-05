@props(['id', 'title'])

<div class="drawer-overlay" id="{{ $id }}">
    <div class="drawer">
        <div class="modal-header">
            <h3>{{ $title }}</h3>
            <button type="button" class="modal-close" data-drawer-close><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            {{ $slot }}
        </div>
        @isset($footer)
            <div class="modal-footer">{{ $footer }}</div>
        @endisset
    </div>
</div>
