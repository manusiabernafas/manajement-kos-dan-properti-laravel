<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #171A21; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .meta { color: #667085; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #F6F7FB; text-align: left; padding: 6px 8px; font-size: 9px; text-transform: uppercase; border-bottom: 1px solid #E6E8EF; }
        td { padding: 6px 8px; border-bottom: 1px solid #E6E8EF; }
        .summary { margin-top: 16px; }
        .summary span { display: inline-block; margin-right: 24px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="meta">Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($headers) }}">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if(!empty($summary))
        <div class="summary">
            @foreach($summary as $label => $value)
                <span>{{ $label }}: {{ $value }}</span>
            @endforeach
        </div>
    @endif
</body>
</html>
