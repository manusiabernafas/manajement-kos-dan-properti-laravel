<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Models\Expense;
use App\Models\Property;
use App\Services\AuditLogService;
use App\Services\NumberGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Expense::class);

        $expenses = Expense::query()
            ->with('property')
            ->when($request->q, fn ($q) => $q->where('expense_number', 'like', "%{$request->q}%")->orWhere('description', 'like', "%{$request->q}%"))
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
            ->when($request->property_id, fn ($q) => $q->where('property_id', $request->property_id))
            ->orderByDesc('date')
            ->paginate(15)
            ->withQueryString();

        return view('expenses.index', [
            'title' => 'Pengeluaran',
            'expenses' => $expenses,
            'properties' => Property::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Expense::class);

        return view('expenses.create', ['title' => 'Tambah Pengeluaran', 'properties' => Property::orderBy('name')->pluck('name', 'id')]);
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['expense_number'] = NumberGeneratorService::generate('expenses', 'expense_number', 'EXP-');
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('expenses', 'public');
        }

        $expense = Expense::create($data);

        AuditLogService::log('create', 'expenses', $expense->id, null, $expense->toArray());

        return redirect()->route('expenses.show', $expense)->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function show(Expense $expense): View
    {
        $this->authorize('view', $expense);

        return view('expenses.show', ['title' => $expense->expense_number, 'expense' => $expense]);
    }

    public function edit(Expense $expense): View
    {
        $this->authorize('update', $expense);

        return view('expenses.edit', ['title' => 'Edit Pengeluaran', 'expense' => $expense, 'properties' => Property::orderBy('name')->pluck('name', 'id')]);
    }

    public function update(StoreExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $old = $expense->toArray();
        $data = $request->validated();

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('expenses', 'public');
        }

        $expense->update($data);

        AuditLogService::log('update', 'expenses', $expense->id, $old, $expense->toArray());

        return redirect()->route('expenses.show', $expense)->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $this->authorize('delete', $expense);

        $expense->delete();

        AuditLogService::log('delete', 'expenses', $expense->id, $expense->toArray(), null);

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
