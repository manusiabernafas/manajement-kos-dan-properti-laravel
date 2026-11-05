<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('rooms.create');
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'building_id' => ['required', 'exists:buildings,id'],
            'floor_id' => ['required', 'exists:floors,id'],
            'room_type_id' => ['required', 'exists:room_types,id'],
            'room_number' => ['required', 'string', 'max:50', Rule::unique('rooms')->where('property_id', $this->property_id)],
            'room_name' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:available,occupied,reserved,maintenance,inactive'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['exists:facilities,id'],
        ];
    }
}
