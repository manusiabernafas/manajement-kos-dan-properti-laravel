<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Check-in']]">
    <x-page-header title="Riwayat Check-in" description="Daftar seluruh proses check-in penghuni">
        <x-slot:actions>
            @can('check-ins.create')
                <a href="{{ route('check-ins.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Check-in Baru</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        @if($checkIns->isEmpty())
            <x-empty-state icon="fa-solid fa-right-to-bracket" title="Belum ada riwayat check-in" description="Proses check-in penghuni pertama untuk memulai.">
                <x-slot:action>
                    @can('check-ins.create')
                        <a href="{{ route('check-ins.create') }}" class="btn btn-primary">+ Check-in Baru</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Penghuni</th><th>Kamar</th><th>Tanggal Check-in</th><th>Kontrak</th><th></th></tr></thead>
                    <tbody>
                        @foreach($checkIns as $checkIn)
                            <tr>
                                <td class="table-cell-primary">{{ $checkIn->tenant->full_name }}</td>
                                <td>{{ $checkIn->room->room_number }}</td>
                                <td>{{ $checkIn->check_in_date->format('d M Y') }}</td>
                                <td><a href="{{ route('contracts.show', $checkIn->contract) }}">{{ $checkIn->contract->contract_number }}</a></td>
                                <td class="text-right"><a href="{{ route('check-ins.show', $checkIn) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $checkIns->links() }}
        @endif
    </div>
</x-layouts.app>
