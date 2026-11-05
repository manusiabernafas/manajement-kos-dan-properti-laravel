<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kontrak {{ $contract->contract_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #171A21; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #171A21; padding-bottom: 16px; }
        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-table td { padding: 4px 0; vertical-align: top; }
        .label { color: #667085; width: 160px; }
        .section-title { font-weight: bold; font-size: 13px; margin: 18px 0 8px; border-bottom: 1px solid #E6E8EF; padding-bottom: 4px; }
        .terms { white-space: pre-line; line-height: 1.6; }
        .signatures { margin-top: 60px; width: 100%; }
        .signatures td { width: 50%; text-align: center; padding-top: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SURAT PERJANJIAN SEWA KOST</h1>
        <p>No. {{ $contract->contract_number }}</p>
    </div>

    <table class="meta-table">
        <tr><td class="label">Nama Penyewa</td><td>: {{ $contract->tenant->full_name }}</td></tr>
        <tr><td class="label">NIK</td><td>: {{ $contract->tenant->nik ?? '-' }}</td></tr>
        <tr><td class="label">Telepon</td><td>: {{ $contract->tenant->phone }}</td></tr>
        <tr><td class="label">Properti</td><td>: {{ $contract->property->name }}</td></tr>
        <tr><td class="label">Kamar</td><td>: {{ $contract->room->room_number }}</td></tr>
        <tr><td class="label">Periode Sewa</td><td>: {{ $contract->start_date->format('d F Y') }} s/d {{ $contract->end_date->format('d F Y') }}</td></tr>
        <tr><td class="label">Harga Sewa</td><td>: Rp {{ number_format($contract->monthly_price, 0, ',', '.') }} / bulan</td></tr>
        <tr><td class="label">Deposit</td><td>: Rp {{ number_format($contract->deposit, 0, ',', '.') }}</td></tr>
        <tr><td class="label">Tanggal Jatuh Tempo</td><td>: Setiap tanggal {{ $contract->billing_day }}</td></tr>
    </table>

    <div class="section-title">Syarat & Ketentuan</div>
    <div class="terms">{{ $contract->terms ?: 'Tidak ada syarat & ketentuan tambahan.' }}</div>

    @if($contract->property->rules)
        <div class="section-title">Aturan Kost</div>
        <div class="terms">{{ $contract->property->rules }}</div>
    @endif

    <table class="signatures">
        <tr>
            <td>
                ( {{ $contract->tenant->full_name }} )<br>Penyewa
            </td>
            <td>
                ( {{ $contract->property->name }} )<br>Pengelola
            </td>
        </tr>
    </table>
</body>
</html>
