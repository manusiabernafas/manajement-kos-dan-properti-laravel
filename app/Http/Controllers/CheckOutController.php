<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckOut\StoreCheckOutRequest;
use App\Models\CheckOut;
use App\Models\Contract;
use App\Services\CheckOutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class CheckOutController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CheckOut::class);

        $checkOuts = CheckOut::query()
            ->with(['tenant', 'room.property'])
            ->orderByDesc('checkout_date')
            ->paginate(15);

        return view('check-outs.index', ['title' => 'Check-out', 'checkOuts' => $checkOuts]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', CheckOut::class);

        $contract = $request->contract_id
            ? Contract::with(['tenant', 'room'])->find($request->contract_id)
            : null;

        return view('check-outs.create', [
            'title' => 'Check-out Penghuni',
            'contracts' => Contract::where('status', 'active')->with(['tenant', 'room'])->get(),
            'selectedContract' => $contract,
        ]);
    }

    public function store(StoreCheckOutRequest $request, CheckOutService $service): RedirectResponse
    {
        try {
            $checkOut = $service->process($request->validated(), $request->user()->id);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('check-outs.show', $checkOut)->with('success', 'Check-out berhasil diproses.');
    }

    public function show(CheckOut $checkOut): View
    {
        $this->authorize('view', $checkOut);

        $checkOut->load(['tenant', 'room.property', 'contract']);

        return view('check-outs.show', ['title' => 'Detail Check-out', 'checkOut' => $checkOut]);
    }
}
