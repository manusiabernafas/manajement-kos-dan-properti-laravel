<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Laporan']]">
    <x-page-header title="Laporan" :description="$types[$type]">
        <x-slot:actions>
            <a href="{{ route('reports.export', array_merge(['format' => 'csv', 'type' => $type], $filters)) }}" class="btn btn-outline"><i class="fa-solid fa-file-csv"></i> CSV</a>
            <a href="{{ route('reports.export', array_merge(['format' => 'xlsx', 'type' => $type], $filters)) }}" class="btn btn-outline"><i class="fa-solid fa-file-excel"></i> Excel</a>
            <a href="{{ route('reports.export', array_merge(['format' => 'pdf', 'type' => $type], $filters)) }}" class="btn btn-outline"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </x-slot:actions>
    </x-page-header>

    <div class="tabs" style="overflow-x:auto;">
        @foreach($types as $key => $label)
            <a href="{{ route('reports.index', ['type' => $key]) }}" class="tab-link {{ $type === $key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="card mb-24">
        <form method="GET" class="card-body" style="padding-bottom:8px;">
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                @if(!in_array($type, ['tenant', 'room', 'outstanding', 'deposit']))
                    <x-input label="Dari Tanggal" name="date_from" type="date" :value="$filters['date_from'] ?? ''" />
                    <x-input label="Sampai Tanggal" name="date_to" type="date" :value="$filters['date_to'] ?? ''" />
                @endif
                @if(in_array($type, ['occupancy', 'revenue', 'outstanding', 'room']))
                    <x-select label="Properti" name="property_id" :options="$properties" :value="$filters['property_id'] ?? ''" />
                @endif
                @if(!empty($statusOptions))
                    <x-select label="Status" name="status" :options="$statusOptions" :value="$filters['status'] ?? ''" />
                @endif
            </div>
            <div class="form-actions" style="justify-content:flex-start; padding-top:0;">
                <x-button type="submit" variant="primary">Terapkan Filter</x-button>
            </div>
        </form>
    </div>

    @if(!empty($result['summary']))
        <div class="grid grid-cols-4 mb-24">
            @foreach($result['summary'] as $label => $value)
                <x-stat-card :label="$label" :value="$value" icon="fa-solid fa-chart-simple" variant="primary" />
            @endforeach
        </div>
    @endif

    <div class="card">
        @if(empty($result['rows']))
            <x-empty-state icon="fa-solid fa-chart-line" title="Tidak ada data" description="Coba ubah filter untuk melihat data lainnya." />
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            @foreach($result['headers'] as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($result['rows'] as $row)
                            <tr>
                                @foreach($row as $value)
                                    <td>{{ $value }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>
