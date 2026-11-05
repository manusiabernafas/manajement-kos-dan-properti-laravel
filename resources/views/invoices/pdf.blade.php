<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #171A21; }
        .header { display: table; width: 100%; margin-bottom: 24px; border-bottom: 2px solid #171A21; padding-bottom: 16px; }
        .header .col { display: table-cell; vertical-align: top; }
        .header .right { text-align: right; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-table td { padding: 3px 0; vertical-align: top; }
        .label { color: #667085; width: 120px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.items th { background: #F6F7FB; text-align: left; padding: 8px; font-size: 10px; text-transform: uppercase; border-bottom: 1px solid #E6E8EF; }
        table.items td { padding: 8px; border-bottom: 1px solid #E6E8EF; }
        .text-right { text-align: right; }
        .totals { width: 280px; margin-left: auto; }
        .totals .row { display: table; width: 100%; padding: 4px 0; }
        .totals .row .col { display: table-cell; }
        .totals .grand { font-weight: bold; font-size: 14px; border-top: 2px solid #171A21; padding-top: 8px; margin-top: 4px; }
        .status { display: inline-block; padding: 4px 10px; border-radius: 10px; font-size: 10px; font-weight: bold; background: #FEF3E2; color: #F79009; }
    </style>
</head>
<body>
    <div class="header">
        <div class="col">
            <h1>INVOICE</h1>
            <p>{{ $invoice->invoice_number }}</p>
        </div>
        <div class="col right">
            <p><strong>{{ $invoice->property->name }}</strong></p>
            <p>{{ $invoice->property->address }}</p>
            <p>{{ $invoice->property->phone }}</p>
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width:50%;">
                <table>
                    <tr><td class="label">Ditagihkan Kepada</td><td>: {{ $invoice->tenant->full_name }}</td></tr>
                    <tr><td class="label">Kamar</td><td>: {{ $invoice->room->room_number }}</td></tr>
                    <tr><td class="label">Telepon</td><td>: {{ $invoice->tenant->phone }}</td></tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr><td class="label">Tanggal Terbit</td><td>: {{ $invoice->issue_date->format('d F Y') }}</td></tr>
                    <tr><td class="label">Jatuh Tempo</td><td>: {{ $invoice->due_date->format('d F Y') }}</td></tr>
                    <tr><td class="label">Status</td><td>: <span class="status">{{ strtoupper($invoice->status) }}</span></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr><th>Deskripsi</th><th class="text-right">Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row"><div class="col">Subtotal</div><div class="col text-right">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</div></div>
        @if($invoice->discount > 0)<div class="row"><div class="col">Diskon</div><div class="col text-right">- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</div></div>@endif
        @if($invoice->penalty > 0)<div class="row"><div class="col">Denda</div><div class="col text-right">Rp {{ number_format($invoice->penalty, 0, ',', '.') }}</div></div>@endif
        @if($invoice->tax > 0)<div class="row"><div class="col">Pajak</div><div class="col text-right">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</div></div>@endif
        <div class="row grand"><div class="col">Total</div><div class="col text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</div></div>
    </div>
</body>
</html>
