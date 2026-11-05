<?php

namespace App\Http\Requests\RoomType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('room-types.create');
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('room_types')->where('property_id', $this->property_id)],
            'description' => ['nullable', 'string'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'weekly_price' => ['nullable', 'numeric', 'min:0'],
            'daily_price' => ['nullable', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'size' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['exists:facilities,id'],
        ];
    }
}
