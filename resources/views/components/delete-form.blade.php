@props(['action', 'label' => 'Hapus', 'message' => 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.'])

<form action="{{ $action }}" method="POST" data-confirm-submit data-confirm-message="{{ $message }}">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->merge(['class' => 'dropdown-item danger']) }}>
        <i class="fa-solid fa-trash"></i> {{ $label }}
    </button>
</form>
