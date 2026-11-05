<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('expenses.create');
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'category' => ['required', 'in:electricity,water,internet,salary,maintenance,cleaning,security,tax,furniture,marketing,operational,other'],
            'date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:cash,bank_transfer,qris,e_wallet,virtual_account,other'],
            'vendor' => ['nullable', 'string', 'max:150'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
