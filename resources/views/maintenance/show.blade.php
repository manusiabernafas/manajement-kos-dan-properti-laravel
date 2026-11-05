<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Maintenance', 'url' => route('maintenance.index')], ['label' => $ticket->ticket_number]]">
    <x-page-header :title="$ticket->title" :description="$ticket->ticket_number">
        <x-slot:actions>
            <x-status-badge :status="$ticket->priority" :colors="\App\Models\MaintenanceTicket::PRIORITY_COLORS" />
            <x-status-badge :status="$ticket->status" :colors="\App\Models\MaintenanceTicket::STATUS_COLORS" />
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-3">
        <div class="card" style="grid-column: span 2;">
            <div class="card-header"><h3>Deskripsi</h3></div>
            <div class="card-body">
                <p class="text-sm">{{ $ticket->description }}</p>
                <div class="grid grid-cols-2 mt-24">
                    <div><div class="text-muted text-sm">Kategori</div><div class="font-medium">{{ $ticket->category }}</div></div>
                    <div><div class="text-muted text-sm">Kamar</div><div class="font-medium">{{ $ticket->room->room_number ?? '-' }}</div></div>
                    <div class="mt-16"><div class="text-muted text-sm">Estimasi Biaya</div><div class="font-medium">{{ $ticket->estimated_cost ? 'Rp '.number_format($ticket->estimated_cost,0,',','.') : '-' }}</div></div>
                    <div class="mt-16"><div class="text-muted text-sm">Biaya Aktual</div><div class="font-medium">{{ $ticket->actual_cost ? 'Rp '.number_format($ticket->actual_cost,0,',','.') : '-' }}</div></div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Update Status</h3></div>
            <div class="card-body">
                @can('maintenance.update')
                    <form action="{{ route('maintenance.update', $ticket) }}" method="POST">
                        @csrf @method('PUT')
                        <x-select label="Status" name="status" required :options="['open'=>'Open','assigned'=>'Assigned','in_progress'=>'In Progress','waiting'=>'Waiting','resolved'=>'Resolved','closed'=>'Closed','cancelled'=>'Cancelled']" :value="$ticket->status" />
                        <x-select label="Ditugaskan Kepada" name="assigned_to" :options="$technicians->mapWithKeys(fn($u) => [$u->id => $u->name])" :value="$ticket->assigned_to" />
                        <x-input label="Biaya Aktual (Rp)" name="actual_cost" type="number" min="0" :value="$ticket->actual_cost" />
                        <x-button type="submit" variant="primary" class="btn-block">Perbarui</x-button>
                    </form>
                @endcan
            </div>
        </div>
    </div>

    <div class="card mt-24">
        <div class="card-header"><h3>Komentar & Riwayat</h3></div>
        <div class="card-body">
            @foreach($ticket->comments as $comment)
                <div class="widget-item" style="padding-left:0;padding-right:0;">
                    <div class="widget-item-icon badge-primary" style="background:var(--color-primary-soft);color:var(--color-primary);"><i class="fa-solid fa-comment"></i></div>
                    <div class="widget-item-body">
                        <div class="widget-item-title">{{ $comment->user->name }}</div>
                        <div class="widget-item-desc">{{ $comment->comment }}</div>
                        <div class="text-xs text-muted mt-8">{{ $comment->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @endforeach

            <form action="{{ route('maintenance.comment', $ticket) }}" method="POST" class="mt-16">
                @csrf
                <x-textarea label="Tambah Komentar" name="comment" required rows="3" />
                <x-button type="submit" variant="primary">Kirim Komentar</x-button>
            </form>
        </div>
    </div>
</x-layouts.app>
