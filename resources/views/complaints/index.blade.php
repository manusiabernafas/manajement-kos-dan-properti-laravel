<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Komplain']]">
    <x-page-header title="Komplain" description="Kelola komplain dari penghuni">
        <x-slot:actions>
            @can('complaints.create')
                <a href="{{ route('complaints.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Komplain</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari komplain...">
                </div>
                <select name="status" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach(['open'=>'Open','assigned'=>'Assigned','in_progress'=>'In Progress','waiting'=>'Waiting','resolved'=>'Resolved','closed'=>'Closed','cancelled'=>'Cancelled'] as $val => $label)
                        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($complaints->isEmpty())
            <x-empty-state icon="fa-solid fa-comment-dots" title="Belum ada komplain" description="Komplain dari penghuni akan muncul di sini." />
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Tiket</th><th>Penghuni</th><th>Prioritas</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                            <tr>
                                <td>
                                    <div class="table-cell-primary">{{ $complaint->title }}</div>
                                    <div class="table-cell-muted">{{ $complaint->ticket_number }}</div>
                                </td>
                                <td>{{ $complaint->tenant->full_name }}</td>
                                <td><x-status-badge :status="$complaint->priority" :colors="\App\Models\Complaint::PRIORITY_COLORS" /></td>
                                <td><x-status-badge :status="$complaint->status" :colors="\App\Models\Complaint::STATUS_COLORS" /></td>
                                <td class="text-right"><a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $complaints->links() }}
        @endif
    </div>
</x-layouts.app>
