<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('maintenance.create');
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'tenant_id' => ['nullable', 'exists:tenants,id'],
            'category' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
