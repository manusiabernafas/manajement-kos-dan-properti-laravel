<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header title="Kamar Saya" />

    @if(!$tenant->activeContract)
        <x-empty-state icon="fa-solid fa-door-open" title="Belum ada kamar aktif" description="Anda belum memiliki kamar yang sedang disewa." />
    @else
        @php $room = $tenant->activeContract->room; @endphp
        <div class="grid grid-cols-3">
            <div class="card">
                <div class="card-header"><h3>Informasi Kamar</h3></div>
                <div class="card-body">
                    <div class="flex justify-between mb-12"><span class="text-muted text-sm">Nomor Kamar</span><span class="font-semibold">{{ $room->room_number }}</span></div>
                    <div class="flex justify-between mb-12"><span class="text-muted text-sm">Properti</span><span>{{ $room->property->name }}</span></div>
                    <div class="flex justify-between mb-12"><span class="text-muted text-sm">Gedung</span><span>{{ $room->building->name }}</span></div>
                    <div class="flex justify-between"><span class="text-muted text-sm">Lantai</span><span>Lantai {{ $room->floor->floor_number }}</span></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Harga</h3></div>
                <div class="card-body">
                    <div class="flex justify-between mb-12"><span class="text-muted text-sm">Harga Bulanan</span><span class="font-semibold">Rp {{ number_format($room->price, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-muted text-sm">Deposit</span><span>Rp {{ number_format($room->deposit, 0, ',', '.') }}</span></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Fasilitas</h3></div>
                <div class="card-body">
                    @forelse($room->facilities as $facility)
                        <span class="badge badge-muted mb-8" style="margin-right:6px;">{{ $facility->name }}</span>
                    @empty
                        <p class="text-muted text-sm">Tidak ada fasilitas terdaftar.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</x-layouts.app>
