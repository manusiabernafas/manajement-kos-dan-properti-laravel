<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header title="Dashboard" :description="'Selamat datang, '.$tenant->full_name" />

    <div class="dashboard-stats">
        <x-stat-card label="Kamar Saya" :value="$tenant->activeContract?->room?->room_number ?? '-'" icon="fa-solid fa-door-open" variant="primary" />
        <x-stat-card label="Total Belum Dibayar" :value="'Rp '.number_format($totalUnpaid, 0, ',', '.')" icon="fa-solid fa-file-invoice-dollar" variant="warning" />
        <x-stat-card label="Jatuh Tempo Berikutnya" :value="$nextDue?->due_date?->format('d M Y') ?? '-'" icon="fa-solid fa-calendar" variant="info" />
        <x-stat-card label="Status Kontrak" :value="$tenant->activeContract ? ucfirst($tenant->activeContract->status) : 'Tidak Ada'" icon="fa-solid fa-file-signature" variant="success" />
    </div>

    <div class="dashboard-row cols-2-even">
        <x-card title="Tagihan Belum Dibayar">
            <x-slot:actions><a href="{{ route('tenant.invoices.index') }}" class="text-sm" style="color:var(--color-primary);font-weight:600;">Lihat Semua</a></x-slot:actions>
            @if($unpaidInvoices->isEmpty())
                <p class="text-muted text-sm" style="padding: 8px 0;">Tidak ada tagihan belum dibayar.</p>
            @else
                <div class="widget-list" style="margin: -22px;">
                    @foreach($unpaidInvoices->take(5) as $invoice)
                        <a href="{{ route('tenant.invoices.show', $invoice) }}" class="widget-item">
                            <div class="widget-item-icon" style="background:var(--color-warning-soft);color:var(--color-warning);"><i class="fa-solid fa-file-invoice"></i></div>
                            <div class="widget-item-body">
                                <div class="widget-item-title">{{ $invoice->invoice_number }}</div>
                                <div class="widget-item-desc">Jatuh tempo {{ $invoice->due_date->format('d M Y') }}</div>
                            </div>
                            <div class="widget-item-value">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card title="Pengumuman Terbaru">
            <x-slot:actions><a href="{{ route('tenant.announcements.index') }}" class="text-sm" style="color:var(--color-primary);font-weight:600;">Lihat Semua</a></x-slot:actions>
            @if($announcements->isEmpty())
                <p class="text-muted text-sm" style="padding: 8px 0;">Belum ada pengumuman.</p>
            @else
                <div class="widget-list" style="margin: -22px;">
                    @foreach($announcements as $announcement)
                        <div class="widget-item">
                            <div class="widget-item-icon" style="background:var(--color-primary-soft);color:var(--color-primary);"><i class="fa-solid fa-bullhorn"></i></div>
                            <div class="widget-item-body">
                                <div class="widget-item-title">{{ $announcement->title }}</div>
                                <div class="widget-item-desc">{{ Str::limit($announcement->content, 60) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>
</x-layouts.app>
