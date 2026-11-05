<?php

namespace App\Http\Requests\CheckOut;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('check-outs.create') || $this->user()->can('check-outs.process');
    }

    public function rules(): array
    {
        return [
            'contract_id' => ['required', 'exists:contracts,id'],
            'checkout_date' => ['required', 'date'],
            'damage_cost' => ['nullable', 'numeric', 'min:0'],
            'penalty_amount' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
