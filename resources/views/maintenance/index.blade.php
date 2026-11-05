<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Maintenance']]">
    <x-page-header title="Maintenance" description="Kelola tiket perbaikan dan perawatan">
        <x-slot:actions>
            @can('maintenance.create')
                <a href="{{ route('maintenance.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Tiket</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tiket...">
                </div>
                <select name="status" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach(['open'=>'Open','assigned'=>'Assigned','in_progress'=>'In Progress','waiting'=>'Waiting','resolved'=>'Resolved','closed'=>'Closed','cancelled'=>'Cancelled'] as $val => $label)
                        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="priority" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Prioritas</option>
                    @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High','urgent'=>'Urgent'] as $val => $label)
                        <option value="{{ $val }}" @selected(request('priority') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($tickets->isEmpty())
            <x-empty-state icon="fa-solid fa-screwdriver-wrench" title="Belum ada tiket maintenance" description="Buat tiket pertama untuk melacak perbaikan.">
                <x-slot:action>
                    @can('maintenance.create')
                        <a href="{{ route('maintenance.create') }}" class="btn btn-primary">+ Tambah Tiket</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Tiket</th><th>Kamar</th><th>Prioritas</th><th>Petugas</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td>
                                    <div class="table-cell-primary">{{ $ticket->title }}</div>
                                    <div class="table-cell-muted">{{ $ticket->ticket_number }}</div>
                                </td>
                                <td>{{ $ticket->room->room_number ?? '-' }}</td>
                                <td><x-status-badge :status="$ticket->priority" :colors="\App\Models\MaintenanceTicket::PRIORITY_COLORS" /></td>
                                <td>{{ $ticket->assignee->name ?? '-' }}</td>
                                <td><x-status-badge :status="$ticket->status" :colors="\App\Models\MaintenanceTicket::STATUS_COLORS" /></td>
                                <td class="text-right"><a href="{{ route('maintenance.show', $ticket) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $tickets->links() }}
        @endif
    </div>
</x-layouts.app>
