<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Property;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $service): View
    {
        abort_unless($request->user()->can('reports.view'), 403);

        $type = $request->get('type', 'occupancy');
        abort_unless(array_key_exists($type, ReportService::TYPES), 404);

        $filters = $request->only(['date_from', 'date_to', 'property_id', 'status', 'tenant_id']);
        $result = $service->generate($type, $filters);

        return view('reports.index', [
            'title' => 'Laporan',
            'type' => $type,
            'types' => ReportService::TYPES,
            'result' => $result,
            'filters' => $filters,
            'properties' => Property::orderBy('name')->pluck('name', 'id'),
            'statusOptions' => $this->statusOptionsFor($type),
        ]);
    }

    protected function statusOptionsFor(string $type): array
    {
        return match ($type) {
            'payment' => ['pending' => 'Pending', 'verified' => 'Verified', 'rejected' => 'Rejected'],
            'tenant' => ['active' => 'Aktif', 'inactive' => 'Nonaktif', 'former' => 'Mantan'],
            'room' => ['available' => 'Available', 'occupied' => 'Occupied', 'reserved' => 'Reserved', 'maintenance' => 'Maintenance', 'inactive' => 'Inactive'],
            'deposit' => ['held' => 'Held', 'partially_refunded' => 'Partially Refunded', 'refunded' => 'Refunded', 'forfeited' => 'Forfeited'],
            'maintenance', 'complaint' => ['open' => 'Open', 'assigned' => 'Assigned', 'in_progress' => 'In Progress', 'waiting' => 'Waiting', 'resolved' => 'Resolved', 'closed' => 'Closed', 'cancelled' => 'Cancelled'],
            default => [],
        };
    }

    public function export(Request $request, ReportService $service, string $format): Response
    {
        abort_unless($request->user()->can('reports.view'), 403);

        $type = $request->get('type', 'occupancy');
        abort_unless(array_key_exists($type, ReportService::TYPES), 404);

        $filters = $request->only(['date_from', 'date_to', 'property_id', 'status', 'tenant_id']);
        $result = $service->generate($type, $filters);
        $filename = 'laporan-'.$type.'-'.now()->format('Ymd-His');

        if ($format === 'csv') {
            return Excel::download(new ReportExport($result['headers'], $result['rows']), "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV);
        }

        if ($format === 'xlsx') {
            return Excel::download(new ReportExport($result['headers'], $result['rows']), "{$filename}.xlsx");
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf', [
                'title' => ReportService::TYPES[$type],
                'headers' => $result['headers'],
                'rows' => $result['rows'],
                'summary' => $result['summary'],
            ]);

            return $pdf->download("{$filename}.pdf");
        }

        abort(404);
    }
}
