<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\Deposit;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\MaintenanceTicket;
use App\Models\MeterReading;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;

class ReportService
{
    public const TYPES = [
        'occupancy' => 'Occupancy Report',
        'revenue' => 'Revenue Report',
        'expense' => 'Expense Report',
        'profit-loss' => 'Profit & Loss',
        'payment' => 'Payment Report',
        'outstanding' => 'Outstanding Invoice',
        'tenant' => 'Tenant Report',
        'room' => 'Room Report',
        'maintenance' => 'Maintenance Report',
        'complaint' => 'Complaint Report',
        'deposit' => 'Deposit Report',
        'meter' => 'Meter Usage Report',
    ];

    protected function rp(float $value): string
    {
        return 'Rp '.number_format($value, 0, ',', '.');
    }

    public function generate(string $type, array $filters): array
    {
        return match ($type) {
            'occupancy' => $this->occupancy($filters),
            'revenue' => $this->revenue($filters),
            'expense' => $this->expense($filters),
            'profit-loss' => $this->profitLoss($filters),
            'payment' => $this->payment($filters),
            'outstanding' => $this->outstanding($filters),
            'tenant' => $this->tenant($filters),
            'room' => $this->room($filters),
            'maintenance' => $this->maintenance($filters),
            'complaint' => $this->complaint($filters),
            'deposit' => $this->deposit($filters),
            'meter' => $this->meter($filters),
            default => ['headers' => [], 'rows' => [], 'summary' => []],
        };
    }

    protected function dateRange(array $filters): array
    {
        $from = $filters['date_from'] ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = $filters['date_to'] ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        return [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()];
    }

    protected function occupancy(array $filters): array
    {
        $properties = Property::when($filters['property_id'] ?? null, fn ($q, $v) => $q->where('id', $v))->get();

        $rows = [];
        foreach ($properties as $property) {
            $counts = $property->rooms()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
            $total = $counts->sum();
            $occupied = $counts['occupied'] ?? 0;
            $active = $total - ($counts['inactive'] ?? 0);
            $rate = $active > 0 ? round(($occupied / $active) * 100, 1) : 0;

            $rows[] = [
                'Properti' => $property->name,
                'Total Kamar' => $total,
                'Occupied' => $occupied,
                'Available' => $counts['available'] ?? 0,
                'Reserved' => $counts['reserved'] ?? 0,
                'Maintenance' => $counts['maintenance'] ?? 0,
                'Occupancy Rate' => $rate.'%',
            ];
        }

        return [
            'headers' => ['Properti', 'Total Kamar', 'Occupied', 'Available', 'Reserved', 'Maintenance', 'Occupancy Rate'],
            'rows' => $rows,
            'summary' => ['Total Properti' => $properties->count()],
        ];
    }

    protected function revenue(array $filters): array
    {
        [$from, $to] = $this->dateRange($filters);

        $payments = Payment::with(['tenant', 'invoice.room'])
            ->where('status', 'verified')
            ->whereBetween('payment_date', [$from, $to])
            ->when($filters['property_id'] ?? null, fn ($q, $v) => $q->whereHas('invoice', fn ($i) => $i->where('property_id', $v)))
            ->when($filters['tenant_id'] ?? null, fn ($q, $v) => $q->where('tenant_id', $v))
            ->orderBy('payment_date')
            ->get();

        $rows = $payments->map(fn ($p) => [
            'Tanggal' => $p->payment_date->format('d M Y'),
            'No. Pembayaran' => $p->payment_number,
            'Penghuni' => $p->tenant->full_name,
            'Kamar' => $p->invoice->room->room_number ?? '-',
            'Metode' => strtoupper(str_replace('_', ' ', $p->payment_method)),
            'Jumlah' => $this->rp((float) $p->amount),
        ])->toArray();

        return [
            'headers' => ['Tanggal', 'No. Pembayaran', 'Penghuni', 'Kamar', 'Metode', 'Jumlah'],
            'rows' => $rows,
            'summary' => ['Total Pendapatan' => $this->rp($payments->sum('amount'))],
        ];
    }

    protected function expense(array $filters): array
    {
        [$from, $to] = $this->dateRange($filters);

        $expenses = Expense::with('property')
            ->whereBetween('date', [$from, $to])
            ->when($filters['property_id'] ?? null, fn ($q, $v) => $q->where('property_id', $v))
            ->orderBy('date')
            ->get();

        $rows = $expenses->map(fn ($e) => [
            'Tanggal' => $e->date->format('d M Y'),
            'No.' => $e->expense_number,
            'Properti' => $e->property->name,
            'Kategori' => ucfirst($e->category),
            'Deskripsi' => $e->description,
            'Jumlah' => $this->rp((float) $e->amount),
        ])->toArray();

        return [
            'headers' => ['Tanggal', 'No.', 'Properti', 'Kategori', 'Deskripsi', 'Jumlah'],
            'rows' => $rows,
            'summary' => ['Total Pengeluaran' => $this->rp($expenses->sum('amount'))],
        ];
    }

    protected function profitLoss(array $filters): array
    {
        [$from, $to] = $this->dateRange($filters);

        $months = [];
        $cursor = $from->copy()->startOfMonth();
        while ($cursor <= $to) {
            $months[] = $cursor->copy();
            $cursor->addMonth();
        }

        $rows = [];
        $totalRevenue = 0;
        $totalExpense = 0;

        foreach ($months as $month) {
            $revenue = (float) Payment::where('status', 'verified')
                ->whereYear('payment_date', $month->year)->whereMonth('payment_date', $month->month)
                ->sum('amount');
            $expense = (float) Expense::whereYear('date', $month->year)->whereMonth('date', $month->month)->sum('amount');

            $rows[] = [
                'Bulan' => $month->translatedFormat('F Y'),
                'Pendapatan' => $this->rp($revenue),
                'Pengeluaran' => $this->rp($expense),
                'Profit' => $this->rp($revenue - $expense),
            ];

            $totalRevenue += $revenue;
            $totalExpense += $expense;
        }

        return [
            'headers' => ['Bulan', 'Pendapatan', 'Pengeluaran', 'Profit'],
            'rows' => $rows,
            'summary' => ['Total Pendapatan' => $this->rp($totalRevenue), 'Total Pengeluaran' => $this->rp($totalExpense), 'Total Profit' => $this->rp($totalRevenue - $totalExpense)],
        ];
    }

    protected function payment(array $filters): array
    {
        [$from, $to] = $this->dateRange($filters);

        $payments = Payment::with(['tenant', 'invoice'])
            ->whereBetween('payment_date', [$from, $to])
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderBy('payment_date')
            ->get();

        $rows = $payments->map(fn ($p) => [
            'Tanggal' => $p->payment_date->format('d M Y'),
            'No. Pembayaran' => $p->payment_number,
            'Penghuni' => $p->tenant->full_name,
            'Invoice' => $p->invoice->invoice_number,
            'Jumlah' => $this->rp((float) $p->amount),
            'Status' => ucfirst($p->status),
        ])->toArray();

        return [
            'headers' => ['Tanggal', 'No. Pembayaran', 'Penghuni', 'Invoice', 'Jumlah', 'Status'],
            'rows' => $rows,
            'summary' => ['Total' => $this->rp($payments->sum('amount'))],
        ];
    }

    protected function outstanding(array $filters): array
    {
        $invoices = Invoice::with(['tenant', 'room'])
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->when($filters['property_id'] ?? null, fn ($q, $v) => $q->where('property_id', $v))
            ->orderBy('due_date')
            ->get();

        $rows = $invoices->map(fn ($i) => [
            'No. Invoice' => $i->invoice_number,
            'Penghuni' => $i->tenant->full_name,
            'Kamar' => $i->room->room_number,
            'Jatuh Tempo' => $i->due_date->format('d M Y'),
            'Total' => $this->rp((float) $i->total),
            'Terbayar' => $this->rp((float) $i->paid_amount),
            'Sisa' => $this->rp((float) $i->remaining_amount),
            'Status' => ucfirst($i->status),
        ])->toArray();

        return [
            'headers' => ['No. Invoice', 'Penghuni', 'Kamar', 'Jatuh Tempo', 'Total', 'Terbayar', 'Sisa', 'Status'],
            'rows' => $rows,
            'summary' => ['Total Piutang' => $this->rp($invoices->sum('remaining_amount'))],
        ];
    }

    protected function tenant(array $filters): array
    {
        $tenants = Tenant::with('activeContract.room')
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderBy('full_name')
            ->get();

        $rows = $tenants->map(fn ($t) => [
            'Kode' => $t->tenant_code,
            'Nama' => $t->full_name,
            'Telepon' => $t->phone,
            'Kamar' => $t->activeContract?->room?->room_number ?? '-',
            'Status' => ucfirst($t->status),
            'Terdaftar' => $t->created_at->format('d M Y'),
        ])->toArray();

        return [
            'headers' => ['Kode', 'Nama', 'Telepon', 'Kamar', 'Status', 'Terdaftar'],
            'rows' => $rows,
            'summary' => ['Total Penghuni' => $tenants->count()],
        ];
    }

    protected function room(array $filters): array
    {
        $rooms = Room::with(['property', 'roomType'])
            ->when($filters['property_id'] ?? null, fn ($q, $v) => $q->where('property_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderBy('room_number')
            ->get();

        $rows = $rooms->map(fn ($r) => [
            'Kamar' => $r->room_number,
            'Properti' => $r->property->name,
            'Tipe' => $r->roomType->name,
            'Harga' => $this->rp((float) $r->price),
            'Status' => ucfirst($r->status),
        ])->toArray();

        return [
            'headers' => ['Kamar', 'Properti', 'Tipe', 'Harga', 'Status'],
            'rows' => $rows,
            'summary' => ['Total Kamar' => $rooms->count()],
        ];
    }

    protected function maintenance(array $filters): array
    {
        [$from, $to] = $this->dateRange($filters);

        $tickets = MaintenanceTicket::with('room')
            ->whereBetween('created_at', [$from, $to])
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('created_at')
            ->get();

        $rows = $tickets->map(fn ($t) => [
            'Tiket' => $t->ticket_number,
            'Judul' => $t->title,
            'Kamar' => $t->room->room_number ?? '-',
            'Prioritas' => ucfirst($t->priority),
            'Status' => ucfirst(str_replace('_', ' ', $t->status)),
            'Dibuat' => $t->created_at->format('d M Y'),
        ])->toArray();

        return [
            'headers' => ['Tiket', 'Judul', 'Kamar', 'Prioritas', 'Status', 'Dibuat'],
            'rows' => $rows,
            'summary' => ['Total Tiket' => $tickets->count()],
        ];
    }

    protected function complaint(array $filters): array
    {
        [$from, $to] = $this->dateRange($filters);

        $complaints = Complaint::with('tenant')
            ->whereBetween('created_at', [$from, $to])
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('created_at')
            ->get();

        $rows = $complaints->map(fn ($c) => [
            'Tiket' => $c->ticket_number,
            'Judul' => $c->title,
            'Penghuni' => $c->tenant->full_name,
            'Prioritas' => ucfirst($c->priority),
            'Status' => ucfirst(str_replace('_', ' ', $c->status)),
            'Dibuat' => $c->created_at->format('d M Y'),
        ])->toArray();

        return [
            'headers' => ['Tiket', 'Judul', 'Penghuni', 'Prioritas', 'Status', 'Dibuat'],
            'rows' => $rows,
            'summary' => ['Total Komplain' => $complaints->count()],
        ];
    }

    protected function deposit(array $filters): array
    {
        $deposits = Deposit::with(['tenant', 'room'])
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('received_date')
            ->get();

        $rows = $deposits->map(fn ($d) => [
            'Penghuni' => $d->tenant->full_name,
            'Kamar' => $d->room->room_number,
            'Jumlah' => $this->rp((float) $d->amount),
            'Refund' => $this->rp((float) ($d->refund_amount ?? 0)),
            'Status' => ucfirst(str_replace('_', ' ', $d->status)),
            'Diterima' => $d->received_date->format('d M Y'),
        ])->toArray();

        return [
            'headers' => ['Penghuni', 'Kamar', 'Jumlah', 'Refund', 'Status', 'Diterima'],
            'rows' => $rows,
            'summary' => ['Total Deposit' => $this->rp($deposits->sum('amount'))],
        ];
    }

    protected function meter(array $filters): array
    {
        [$from, $to] = $this->dateRange($filters);

        $readings = MeterReading::with('meter.room')
            ->whereBetween('reading_date', [$from, $to])
            ->orderBy('reading_date')
            ->get();

        $rows = $readings->map(fn ($r) => [
            'Kamar' => $r->meter->room->room_number ?? '-',
            'Tipe' => ucfirst($r->meter->meter_type ?? '-'),
            'Tanggal' => $r->reading_date->format('d M Y'),
            'Awal' => (float) $r->previous_value,
            'Akhir' => (float) $r->current_value,
            'Pemakaian' => (float) $r->usage,
            'Total' => $this->rp((float) $r->total),
        ])->toArray();

        return [
            'headers' => ['Kamar', 'Tipe', 'Tanggal', 'Awal', 'Akhir', 'Pemakaian', 'Total'],
            'rows' => $rows,
            'summary' => ['Total Tagihan Meter' => $this->rp($readings->sum('total'))],
        ];
    }
}
