<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header title="Maintenance Saya">
        <x-slot:actions>
            <a href="{{ route('tenant.maintenance.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Ajukan Maintenance</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        @if($tickets->isEmpty())
            <x-empty-state icon="fa-solid fa-screwdriver-wrench" title="Belum ada permintaan maintenance" />
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Tiket</th><th>Judul</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td class="table-cell-primary">{{ $ticket->ticket_number }}</td>
                                <td>{{ $ticket->title }}</td>
                                <td><x-status-badge :status="$ticket->status" :colors="\App\Models\MaintenanceTicket::STATUS_COLORS" /></td>
                                <td class="text-right"><a href="{{ route('tenant.maintenance.show', $ticket) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $tickets->links() }}
        @endif
    </div>
</x-layouts.app>
