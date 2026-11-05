<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('audit-logs.view'), 403);

        $logs = AuditLog::query()
            ->with('user')
            ->when($request->user_id, fn ($q, $v) => $q->where('user_id', $v))
            ->when($request->module, fn ($q, $v) => $q->where('module', $v))
            ->when($request->action, fn ($q, $v) => $q->where('action', $v))
            ->when($request->date_from, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('audit-logs.index', [
            'title' => 'Audit Logs',
            'logs' => $logs,
            'users' => User::orderBy('name')->pluck('name', 'id'),
            'modules' => AuditLog::query()->distinct()->orderBy('module')->pluck('module'),
        ]);
    }
}
