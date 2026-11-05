<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Pembayaran', 'url' => route('payments.index')], ['label' => $payment->payment_number]]">
    <x-page-header :title="$payment->payment_number" :description="$payment->tenant->full_name">
        <x-slot:actions>
            <x-status-badge :status="$payment->status" :colors="\App\Models\Payment::STATUS_COLORS" />
            @if($payment->status === 'pending')
                @can('payments.verify')
                    <form action="{{ route('payments.verify', $payment) }}" method="POST" data-confirm-submit data-confirm-danger="false" data-confirm-message="Verifikasi pembayaran ini?" data-confirm-text="Ya, Verifikasi">
                        @csrf
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Verifikasi</button>
                    </form>
                    <form action="{{ route('payments.reject', $payment) }}" method="POST" data-confirm-submit data-confirm-message="Tolak pembayaran ini?" data-confirm-text="Ya, Tolak">
                        @csrf
                        <button type="submit" class="btn btn-danger"><i class="fa-solid fa-xmark"></i> Tolak</button>
                    </form>
                @endcan
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-3">
        <div class="card">
            <div class="card-header"><h3>Detail Pembayaran</h3></div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Jumlah</span><span class="font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Tanggal</span><span>{{ $payment->payment_date->format('d M Y') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Metode</span><span>{{ strtoupper(str_replace('_',' ',$payment->payment_method)) }}</span></div>
                <div class="flex justify-between"><span class="text-muted text-sm">Referensi</span><span>{{ $payment->reference_number ?? '-' }}</span></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Tagihan</h3></div>
            <div class="card-body">
                <p class="font-semibold">{{ $payment->invoice->invoice_number }}</p>
                <p class="text-muted text-sm mt-8">Total: Rp {{ number_format($payment->invoice->total, 0, ',', '.') }}</p>
                <a href="{{ route('invoices.show', $payment->invoice) }}" class="btn btn-outline btn-sm mt-16">Lihat Tagihan</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Bukti Pembayaran</h3></div>
            <div class="card-body">
                @if($payment->proof_path)
                    <a href="{{ asset('storage/'.$payment->proof_path) }}" target="_blank">
                        <img src="{{ asset('storage/'.$payment->proof_path) }}" style="width:100%; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    </a>
                @else
                    <p class="text-muted text-sm">Tidak ada bukti pembayaran.</p>
                @endif
            </div>
        </div>
    </div>

    @if($payment->status === 'verified')
        <x-alert variant="success" class="mt-24">Diverifikasi oleh {{ $payment->verifier?->name }} pada {{ $payment->verified_at?->format('d M Y H:i') }}</x-alert>
    @elseif($payment->status === 'rejected')
        <x-alert variant="danger" class="mt-24">Ditolak oleh {{ $payment->verifier?->name }} pada {{ $payment->verified_at?->format('d M Y H:i') }}. {{ $payment->notes }}</x-alert>
    @endif
</x-layouts.app>
