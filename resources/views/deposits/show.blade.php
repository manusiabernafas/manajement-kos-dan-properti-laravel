<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Deposit', 'url' => route('deposits.index')], ['label' => $deposit->tenant->full_name]]">
    <x-page-header :title="'Deposit — '.$deposit->tenant->full_name" :description="$deposit->room->room_number">
        <x-slot:actions>
            <x-status-badge :status="$deposit->status" :colors="\App\Models\Deposit::STATUS_COLORS" />
            @can('deposits.update')
                <button type="button" class="btn btn-outline" data-modal-open="modal-edit-deposit"><i class="fa-regular fa-pen-to-square"></i> Kelola Refund</button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-2">
        <div class="card">
            <div class="card-header"><h3>Detail Deposit</h3></div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Jumlah Diterima</span><span class="font-semibold">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Tanggal Diterima</span><span>{{ $deposit->received_date->format('d M Y') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Potongan</span><span>Rp {{ number_format($deposit->deduction, 0, ',', '.') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Jumlah Refund</span><span>Rp {{ number_format($deposit->refund_amount ?? 0, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-muted text-sm">Tanggal Refund</span><span>{{ $deposit->refund_date?->format('d M Y') ?? '-' }}</span></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Kontrak Terkait</h3></div>
            <div class="card-body">
                <p class="font-semibold">{{ $deposit->contract->contract_number }}</p>
                <a href="{{ route('contracts.show', $deposit->contract) }}" class="btn btn-outline btn-sm mt-16">Lihat Kontrak</a>
            </div>
        </div>
    </div>

    <x-modal id="modal-edit-deposit" title="Kelola Refund Deposit">
        <form action="{{ route('deposits.update', $deposit) }}" method="POST" id="form-edit-deposit">
            @csrf @method('PUT')
            <x-input label="Jumlah Refund (Rp)" name="refund_amount" type="number" min="0" :value="$deposit->refund_amount ?? 0" required />
            <x-input label="Potongan (Rp)" name="deduction" type="number" min="0" :value="$deposit->deduction" required />
            <x-input label="Tanggal Refund" name="refund_date" type="date" :value="$deposit->refund_date?->format('Y-m-d') ?? now()->format('Y-m-d')" required />
            <x-select label="Status" name="status" required :options="['held' => 'Held', 'partially_refunded' => 'Partially Refunded', 'refunded' => 'Refunded', 'forfeited' => 'Forfeited']" :value="$deposit->status" />
            <x-textarea label="Catatan" name="notes" :value="$deposit->notes" />
        </form>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
            <button type="submit" form="form-edit-deposit" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-modal>
</x-layouts.app>
