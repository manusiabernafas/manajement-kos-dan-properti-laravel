<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header title="Komplain Saya">
        <x-slot:actions>
            <a href="{{ route('tenant.complaints.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Buat Komplain</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        @if($complaints->isEmpty())
            <x-empty-state icon="fa-solid fa-comment-dots" title="Belum ada komplain" />
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Tiket</th><th>Judul</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                            <tr>
                                <td class="table-cell-primary">{{ $complaint->ticket_number }}</td>
                                <td>{{ $complaint->title }}</td>
                                <td><x-status-badge :status="$complaint->status" :colors="\App\Models\Complaint::STATUS_COLORS" /></td>
                                <td class="text-right"><a href="{{ route('tenant.complaints.show', $complaint) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $complaints->links() }}
        @endif
    </div>
</x-layouts.app>
