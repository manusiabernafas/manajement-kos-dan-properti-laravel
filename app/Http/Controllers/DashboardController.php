<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Contract;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\MaintenanceTicket;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasRole('Tenant')) {
            return redirect()->route('tenant.dashboard');
        }

        abort_unless($request->user()->can('reports.view') || $request->user()->hasAnyRole(['Super Admin', 'Owner', 'Manager', 'Finance', 'Staff', 'Technician']), 403);

        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        $totalProperties = Property::count();
        $totalBuildings = \App\Models\Building::count();
        $totalRooms = Room::count();
        $roomStatusCounts = Room::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $occupiedRooms = $roomStatusCounts['occupied'] ?? 0;
        $availableRooms = $roomStatusCounts['available'] ?? 0;
        $maintenanceRooms = $roomStatusCounts['maintenance'] ?? 0;
        $activeRooms = $totalRooms - ($roomStatusCounts['inactive'] ?? 0);
        $occupancyRate = $activeRooms > 0 ? round(($occupiedRooms / $activeRooms) * 100, 1) : 0;

        $totalTenants = Tenant::where('status', 'active')->count();

        $revenueThisMonth = (float) Payment::where('status', 'verified')
            ->whereBetween('payment_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $expenseThisMonth = (float) Expense::whereBetween('date', [$monthStart, $monthEnd])->sum('amount');
        $profitThisMonth = $revenueThisMonth - $expenseThisMonth;

        $outstanding = (float) Invoice::whereIn('status', ['unpaid', 'partial', 'overdue'])->sum('remaining_amount');

        // Revenue vs Expense last 6 months
        $months = collect(range(5, 0))->map(fn ($i) => $today->copy()->subMonths($i));
        $revenueExpenseLabels = $months->map(fn ($m) => $m->translatedFormat('M Y'))->toArray();
        $revenueSeries = $months->map(function ($m) {
            return (float) Payment::where('status', 'verified')
                ->whereYear('payment_date', $m->year)->whereMonth('payment_date', $m->month)
                ->sum('amount');
        })->toArray();
        $expenseSeries = $months->map(function ($m) {
            return (float) Expense::whereYear('date', $m->year)->whereMonth('date', $m->month)->sum('amount');
        })->toArray();

        // Revenue 12 month trend
        $months12 = collect(range(11, 0))->map(fn ($i) => $today->copy()->subMonths($i));
        $revenue12Labels = $months12->map(fn ($m) => $m->translatedFormat('M'))->toArray();
        $revenue12Series = $months12->map(function ($m) {
            return (float) Payment::where('status', 'verified')
                ->whereYear('payment_date', $m->year)->whereMonth('payment_date', $m->month)
                ->sum('amount');
        })->toArray();

        $roomStatusLabels = ['Available', 'Occupied', 'Reserved', 'Maintenance', 'Inactive'];
        $roomStatusSeries = [
            $roomStatusCounts['available'] ?? 0,
            $roomStatusCounts['occupied'] ?? 0,
            $roomStatusCounts['reserved'] ?? 0,
            $roomStatusCounts['maintenance'] ?? 0,
            $roomStatusCounts['inactive'] ?? 0,
        ];

        $dueSoonInvoices = Invoice::with(['tenant', 'room'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereBetween('due_date', [$today, $today->copy()->addDays(7)])
            ->orderBy('due_date')->limit(6)->get();

        $overdueInvoices = Invoice::with(['tenant', 'room'])
            ->where('status', 'overdue')
            ->orderBy('due_date')->limit(6)->get();

        $recentPayments = Payment::with(['tenant', 'invoice'])
            ->where('status', 'verified')
            ->latest('verified_at')->limit(6)->get();

        $recentTenants = Tenant::latest()->limit(6)->get();

        $recentBookings = Booking::with(['tenant', 'room'])->latest()->limit(6)->get();

        $recentMaintenance = MaintenanceTicket::with('room')->latest()->limit(5)->get();

        $recentComplaints = Complaint::with('tenant')->latest()->limit(5)->get();

        $expiringContracts = Contract::with(['tenant', 'room'])
            ->where('status', 'active')
            ->whereBetween('end_date', [$today, $today->copy()->addDays(30)])
            ->orderBy('end_date')->limit(6)->get();

        return view('dashboard.index', [
            'title' => 'Dashboard',
            'stats' => [
                'total_properties' => $totalProperties,
                'total_buildings' => $totalBuildings,
                'total_rooms' => $totalRooms,
                'occupied_rooms' => $occupiedRooms,
                'available_rooms' => $availableRooms,
                'maintenance_rooms' => $maintenanceRooms,
                'occupancy_rate' => $occupancyRate,
                'total_tenants' => $totalTenants,
                'revenue_this_month' => $revenueThisMonth,
                'expense_this_month' => $expenseThisMonth,
                'profit_this_month' => $profitThisMonth,
                'outstanding' => $outstanding,
            ],
            'charts' => [
                'revenue_expense_labels' => $revenueExpenseLabels,
                'revenue_series' => $revenueSeries,
                'expense_series' => $expenseSeries,
                'revenue12_labels' => $revenue12Labels,
                'revenue12_series' => $revenue12Series,
                'room_status_labels' => $roomStatusLabels,
                'room_status_series' => $roomStatusSeries,
            ],
            'dueSoonInvoices' => $dueSoonInvoices,
            'overdueInvoices' => $overdueInvoices,
            'recentPayments' => $recentPayments,
            'recentTenants' => $recentTenants,
            'recentBookings' => $recentBookings,
            'recentMaintenance' => $recentMaintenance,
            'recentComplaints' => $recentComplaints,
            'expiringContracts' => $expiringContracts,
        ]);
    }
}
