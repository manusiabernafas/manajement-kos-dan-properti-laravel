<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payments.create');
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank_transfer,qris,e_wallet,virtual_account,other'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'proof' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
