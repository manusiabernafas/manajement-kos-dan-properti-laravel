<x-layouts.app :title="$title">
    <x-page-header title="Dashboard" description="Ringkasan operasional KostManager" />

    <div class="dashboard-stats">
        <x-stat-card label="Pendapatan Bulan Ini" :value="'Rp '.number_format($stats['revenue_this_month'], 0, ',', '.')" icon="fa-solid fa-arrow-trend-up" variant="success" />
        <x-stat-card label="Pengeluaran Bulan Ini" :value="'Rp '.number_format($stats['expense_this_month'], 0, ',', '.')" icon="fa-solid fa-arrow-trend-down" variant="danger" />
        <x-stat-card label="Profit Bulan Ini" :value="'Rp '.number_format($stats['profit_this_month'], 0, ',', '.')" icon="fa-solid fa-scale-balanced" variant="primary" />
        <x-stat-card label="Occupancy Rate" :value="$stats['occupancy_rate'].'%'" icon="fa-solid fa-chart-pie" variant="info" />
    </div>

    <div class="dashboard-stats">
        <x-stat-card label="Total Kamar" :value="$stats['total_rooms']" icon="fa-solid fa-door-open" variant="primary" />
        <x-stat-card label="Kamar Terisi" :value="$stats['occupied_rooms']" icon="fa-solid fa-user-check" variant="info" />
        <x-stat-card label="Kamar Kosong" :value="$stats['available_rooms']" icon="fa-solid fa-door-closed" variant="success" />
        <x-stat-card label="Kamar Maintenance" :value="$stats['maintenance_rooms']" icon="fa-solid fa-screwdriver-wrench" variant="warning" />
    </div>

    <div class="dashboard-stats">
        <x-stat-card label="Total Properti" :value="$stats['total_properties']" icon="fa-solid fa-city" variant="primary" />
        <x-stat-card label="Total Gedung" :value="$stats['total_buildings']" icon="fa-solid fa-building" variant="info" />
        <x-stat-card label="Total Penghuni" :value="$stats['total_tenants']" icon="fa-solid fa-users" variant="success" />
        <x-stat-card label="Piutang" :value="'Rp '.number_format($stats['outstanding'], 0, ',', '.')" icon="fa-solid fa-hourglass-half" variant="warning" />
    </div>

    <div class="dashboard-row">
        <x-card title="Pendapatan vs Pengeluaran (6 Bulan Terakhir)">
            <div class="chart-wrapper"><canvas id="chart-revenue-expense"></canvas></div>
        </x-card>
        <x-card title="Status Kamar">
            <div class="chart-wrapper sm"><canvas id="chart-room-status"></canvas></div>
        </x-card>
    </div>

    <div class="dashboard-row cols-2-even">
        <x-card title="Tren Pendapatan 12 Bulan">
            <div class="chart-wrapper sm"><canvas id="chart-revenue-12"></canvas></div>
        </x-card>
        <x-card title="Occupancy Rate">
            <div class="chart-wrapper sm"><canvas id="chart-occupancy"></canvas></div>
        </x-card>
    </div>

    <div class="dashboard-row cols-2-even">
        <x-card title="Tagihan Jatuh Tempo (7 Hari)">
            <x-slot:actions><a href="{{ route('invoices.index', ['status' => 'unpaid']) }}" class="text-sm" style="color:var(--color-primary);font-weight:600;">Lihat Semua</a></x-slot:actions>
            @if($dueSoonInvoices->isEmpty())
                <p class="text-muted text-sm" style="padding: 8px 0;">Tidak ada tagihan jatuh tempo dalam 7 hari.</p>
            @else
                <div class="widget-list" style="margin: -22px;">
                    @foreach($dueSoonInvoices as $invoice)
                        <a href="{{ route('invoices.show', $invoice) }}" class="widget-item">
                            <div class="widget-item-icon" style="background:var(--color-warning-soft);color:var(--color-warning);"><i class="fa-solid fa-file-invoice"></i></div>
                            <div class="widget-item-body">
                                <div class="widget-item-title">{{ $invoice->tenant->full_name }} — {{ $invoice->room->room_number }}</div>
                                <div class="widget-item-desc">Jatuh tempo {{ $invoice->due_date->format('d M Y') }}</div>
                            </div>
                            <div class="widget-item-value">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card title="Tagihan Overdue">
            <x-slot:actions><a href="{{ route('invoices.index', ['status' => 'overdue']) }}" class="text-sm" style="color:var(--color-primary);font-weight:600;">Lihat Semua</a></x-slot:actions>
            @if($overdueInvoices->isEmpty())
                <p class="text-muted text-sm" style="padding: 8px 0;">Tidak ada tagihan overdue.</p>
            @else
                <div class="widget-list" style="margin: -22px;">
                    @foreach($overdueInvoices as $invoice)
                        <a href="{{ route('invoices.show', $invoice) }}" class="widget-item">
                            <div class="widget-item-icon" style="background:var(--color-danger-soft);color:var(--color-danger);"><i class="fa-solid fa-triangle-exclamation"></i></div>
                            <div class="widget-item-body">
                                <div class="widget-item-title">{{ $invoice->tenant->full_name }} — {{ $invoice->room->room_number }}</div>
                                <div class="widget-item-desc">Jatuh tempo {{ $invoice->due_date->format('d M Y') }}</div>
                            </div>
                            <div class="widget-item-value">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    <div class="dashboard-row cols-2-even">
        <x-card title="Pembayaran Terbaru">
            <x-slot:actions><a href="{{ route('payments.index') }}" class="text-sm" style="color:var(--color-primary);font-weight:600;">Lihat Semua</a></x-slot:actions>
            @if($recentPayments->isEmpty())
                <p class="text-muted text-sm" style="padding: 8px 0;">Belum ada pembayaran.</p>
            @else
                <div class="widget-list" style="margin: -22px;">
                    @foreach($recentPayments as $payment)
                        <a href="{{ route('payments.show', $payment) }}" class="widget-item">
                            <div class="widget-item-icon" style="background:var(--color-success-soft);color:var(--color-success);"><i class="fa-solid fa-money-check-dollar"></i></div>
                            <div class="widget-item-body">
                                <div class="widget-item-title">{{ $payment->tenant->full_name }}</div>
                                <div class="widget-item-desc">{{ $payment->payment_date->format('d M Y') }}</div>
                            </div>
                            <div class="widget-item-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card title="Penghuni Terbaru">
            <x-slot:actions><a href="{{ route('tenants.index') }}" class="text-sm" style="color:var(--color-primary);font-weight:600;">Lihat Semua</a></x-slot:actions>
            @if($recentTenants->isEmpty())
                <p class="text-muted text-sm" style="padding: 8px 0;">Belum ada penghuni.</p>
            @else
                <div class="widget-list" style="margin: -22px;">
                    @foreach($recentTenants as $tenant)
                        <a href="{{ route('tenants.show', $tenant) }}" class="widget-item">
                            <div class="widget-item-icon" style="background:var(--color-primary-soft);color:var(--color-primary);"><i class="fa-solid fa-user"></i></div>
                            <div class="widget-item-body">
                                <div class="widget-item-title">{{ $tenant->full_name }}</div>
                                <div class="widget-item-desc">{{ $tenant->tenant_code }}</div>
                            </div>
                            <div class="widget-item-value">{{ $tenant->created_at->diffForHumans() }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    <div class="dashboard-row cols-2-even">
        <x-card title="Booking Terbaru">
            <x-slot:actions><a href="{{ route('bookings.index') }}" class="text-sm" style="color:var(--color-primary);font-weight:600;">Lihat Semua</a></x-slot:actions>
            @if($recentBookings->isEmpty())
                <p class="text-muted text-sm" style="padding: 8px 0;">Belum ada booking.</p>
            @else
                <div class="widget-list" style="margin: -22px;">
                    @foreach($recentBookings as $booking)
                        <a href="{{ route('bookings.show', $booking) }}" class="widget-item">
                            <div class="widget-item-icon" style="background:var(--color-info-soft);color:var(--color-info);"><i class="fa-solid fa-calendar-check"></i></div>
                            <div class="widget-item-body">
                                <div class="widget-item-title">{{ $booking->tenant->full_name }} — {{ $booking->room->room_number }}</div>
                                <div class="widget-item-desc">{{ ucfirst($booking->status) }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card title="Kontrak Akan Berakhir (30 Hari)">
            <x-slot:actions><a href="{{ route('contracts.index') }}" class="text-sm" style="color:var(--color-primary);font-weight:600;">Lihat Semua</a></x-slot:actions>
            @if($expiringContracts->isEmpty())
                <p class="text-muted text-sm" style="padding: 8px 0;">Tidak ada kontrak akan berakhir.</p>
            @else
                <div class="widget-list" style="margin: -22px;">
                    @foreach($expiringContracts as $contract)
                        <a href="{{ route('contracts.show', $contract) }}" class="widget-item">
                            <div class="widget-item-icon" style="background:var(--color-warning-soft);color:var(--color-warning);"><i class="fa-solid fa-file-signature"></i></div>
                            <div class="widget-item-body">
                                <div class="widget-item-title">{{ $contract->tenant->full_name }} — {{ $contract->room->room_number }}</div>
                                <div class="widget-item-desc">Berakhir {{ $contract->end_date->format('d M Y') }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    <div class="dashboard-row cols-2-even">
        <x-card title="Maintenance Terbaru">
            <x-slot:actions><a href="{{ route('maintenance.index') }}" class="text-sm" style="color:var(--color-primary);font-weight:600;">Lihat Semua</a></x-slot:actions>
            @if($recentMaintenance->isEmpty())
                <p class="text-muted text-sm" style="padding: 8px 0;">Belum ada tiket maintenance.</p>
            @else
                <div class="widget-list" style="margin: -22px;">
                    @foreach($recentMaintenance as $ticket)
                        <a href="{{ route('maintenance.show', $ticket) }}" class="widget-item">
                            <div class="widget-item-icon" style="background:var(--color-warning-soft);color:var(--color-warning);"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                            <div class="widget-item-body">
                                <div class="widget-item-title">{{ $ticket->title }}</div>
                                <div class="widget-item-desc">{{ $ticket->room->room_number ?? '-' }} · {{ ucfirst($ticket->status) }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card title="Komplain Terbaru">
            <x-slot:actions><a href="{{ route('complaints.index') }}" class="text-sm" style="color:var(--color-primary);font-weight:600;">Lihat Semua</a></x-slot:actions>
            @if($recentComplaints->isEmpty())
                <p class="text-muted text-sm" style="padding: 8px 0;">Belum ada komplain.</p>
            @else
                <div class="widget-list" style="margin: -22px;">
                    @foreach($recentComplaints as $complaint)
                        <a href="{{ route('complaints.show', $complaint) }}" class="widget-item">
                            <div class="widget-item-icon" style="background:var(--color-danger-soft);color:var(--color-danger);"><i class="fa-solid fa-comment-dots"></i></div>
                            <div class="widget-item-body">
                                <div class="widget-item-title">{{ $complaint->title }}</div>
                                <div class="widget-item-desc">{{ $complaint->tenant->full_name }} · {{ ucfirst($complaint->status) }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function render() {
                if (!window.KMCharts) { setTimeout(render, 50); return; }
                window.KMCharts.renderRevenueExpenseChart('chart-revenue-expense',
                    @json($charts['revenue_expense_labels']),
                    @json($charts['revenue_series']),
                    @json($charts['expense_series'])
                );
                window.KMCharts.renderRoomStatusChart('chart-room-status',
                    @json($charts['room_status_labels']),
                    @json($charts['room_status_series'])
                );
                window.KMCharts.renderRevenue12MonthChart('chart-revenue-12',
                    @json($charts['revenue12_labels']),
                    @json($charts['revenue12_series'])
                );
                window.KMCharts.renderOccupancyChart('chart-occupancy',
                    ['Occupancy Rate'],
                    [{{ $stats['occupancy_rate'] }}]
                );
            }
            render();
        });
    </script>
    @endpush
</x-layouts.app>
