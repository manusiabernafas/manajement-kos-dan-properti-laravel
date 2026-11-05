<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('bookings.create');
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'exists:tenants,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'booking_date' => ['required', 'date'],
            'check_in_date' => ['required', 'date', 'after_or_equal:booking_date'],
            'expected_checkout_date' => ['nullable', 'date', 'after:check_in_date'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
