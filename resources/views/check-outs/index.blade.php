<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Check-out']]">
    <x-page-header title="Riwayat Check-out" description="Daftar seluruh proses check-out penghuni">
        <x-slot:actions>
            @can('check-outs.create')
                <a href="{{ route('check-outs.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Check-out Baru</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        @if($checkOuts->isEmpty())
            <x-empty-state icon="fa-solid fa-right-from-bracket" title="Belum ada riwayat check-out" />
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Penghuni</th><th>Kamar</th><th>Tanggal Checkout</th><th class="text-right">Refund Deposit</th><th></th></tr></thead>
                    <tbody>
                        @foreach($checkOuts as $checkOut)
                            <tr>
                                <td class="table-cell-primary">{{ $checkOut->tenant->full_name }}</td>
                                <td>{{ $checkOut->room->room_number }}</td>
                                <td>{{ $checkOut->checkout_date->format('d M Y') }}</td>
                                <td class="text-right">Rp {{ number_format($checkOut->refund_amount, 0, ',', '.') }}</td>
                                <td class="text-right"><a href="{{ route('check-outs.show', $checkOut) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $checkOuts->links() }}
        @endif
    </div>
</x-layouts.app>
