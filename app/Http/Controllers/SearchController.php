<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Room;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Contract;
use App\Models\MaintenanceTicket;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        abort_if($request->user()->hasRole('Tenant'), 403);

        $q = trim((string) $request->get('q'));
        $results = [];

        if ($q !== '') {
            $results['tenants'] = Tenant::where('full_name', 'like', "%{$q}%")
                ->orWhere('tenant_code', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->limit(5)->get();

            $results['rooms'] = Room::where('room_number', 'like', "%{$q}%")
                ->orWhere('room_name', 'like', "%{$q}%")
                ->limit(5)->get();

            $results['invoices'] = Invoice::where('invoice_number', 'like', "%{$q}%")->limit(5)->get();
            $results['payments'] = Payment::where('payment_number', 'like', "%{$q}%")->limit(5)->get();
            $results['bookings'] = Booking::where('booking_number', 'like', "%{$q}%")->limit(5)->get();
            $results['contracts'] = Contract::where('contract_number', 'like', "%{$q}%")->limit(5)->get();
            $results['maintenance'] = MaintenanceTicket::where('ticket_number', 'like', "%{$q}%")
                ->orWhere('title', 'like', "%{$q}%")->limit(5)->get();
            $results['complaints'] = Complaint::where('ticket_number', 'like', "%{$q}%")
                ->orWhere('title', 'like', "%{$q}%")->limit(5)->get();
        }

        return view('search.index', ['title' => 'Hasil Pencarian', 'q' => $q, 'results' => $results]);
    }
}
