<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Audit Logs']]">
    <x-page-header title="Audit Logs" description="Riwayat aktivitas penting dalam sistem" />

    <div class="card mb-24">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <select name="user_id" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua User</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" @selected(request('user_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="module" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" @selected(request('module') === $module)>{{ ucfirst($module) }}</option>
                    @endforeach
                </select>
                <select name="action" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Aksi</option>
                    <option value="create" @selected(request('action') === 'create')>Create</option>
                    <option value="update" @selected(request('action') === 'update')>Update</option>
                    <option value="delete" @selected(request('action') === 'delete')>Delete</option>
                    <option value="login" @selected(request('action') === 'login')>Login</option>
                    <option value="logout" @selected(request('action') === 'logout')>Logout</option>
                    <option value="verify_payment" @selected(request('action') === 'verify_payment')>Verify Payment</option>
                    <option value="check_in" @selected(request('action') === 'check_in')>Check-in</option>
                    <option value="check_out" @selected(request('action') === 'check_out')>Check-out</option>
                </select>
            </div>
        </form>
    </div>

    <div class="card">
        @if($logs->isEmpty())
            <x-empty-state icon="fa-solid fa-clipboard-list" title="Belum ada aktivitas tercatat" />
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Modul</th><th>IP</th></tr></thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td class="table-cell-muted">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                <td>{{ $log->user->name ?? 'System' }}</td>
                                <td><span class="badge badge-muted">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span></td>
                                <td>{{ ucfirst($log->module) }}</td>
                                <td class="table-cell-muted">{{ $log->ip_address }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $logs->links() }}
        @endif
    </div>
</x-layouts.app>
