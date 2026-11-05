<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header :title="$payment->payment_number">
        <x-slot:actions>
            <x-status-badge :status="$payment->status" :colors="\App\Models\Payment::STATUS_COLORS" />
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-2">
        <div class="card">
            <div class="card-header"><h3>Detail</h3></div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Jumlah</span><span class="font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Tanggal</span><span>{{ $payment->payment_date->format('d M Y') }}</span></div>
                <div class="flex justify-between"><span class="text-muted text-sm">Invoice</span><span><a href="{{ route('tenant.invoices.show', $payment->invoice) }}">{{ $payment->invoice->invoice_number }}</a></span></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Bukti Pembayaran</h3></div>
            <div class="card-body">
                @if($payment->proof_path)
                    <img src="{{ asset('storage/'.$payment->proof_path) }}" style="width:100%; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                @endif
            </div>
        </div>
    </div>

    @if($payment->status === 'pending')
        <x-alert variant="warning" class="mt-24">Pembayaran Anda sedang menunggu verifikasi dari admin.</x-alert>
    @elseif($payment->status === 'verified')
        <x-alert variant="success" class="mt-24">Pembayaran Anda telah diverifikasi.</x-alert>
    @elseif($payment->status === 'rejected')
        <x-alert variant="danger" class="mt-24">Pembayaran Anda ditolak. {{ $payment->notes }}</x-alert>
    @endif
</x-layouts.app>
