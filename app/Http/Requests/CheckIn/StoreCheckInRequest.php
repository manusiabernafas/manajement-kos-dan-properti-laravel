<?php

namespace App\Http\Requests\CheckIn;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('check-ins.create') || $this->user()->can('check-ins.process');
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'exists:tenants,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'check_in_date' => ['required', 'date'],
            'billing_date' => ['required', 'date'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:36'],
            'terms' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
