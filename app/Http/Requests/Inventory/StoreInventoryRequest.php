<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('inventories.create');
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'category' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'condition' => ['required', 'in:good,minor_damage,major_damage,lost'],
            'status' => ['required', 'in:active,maintenance,lost,retired'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
