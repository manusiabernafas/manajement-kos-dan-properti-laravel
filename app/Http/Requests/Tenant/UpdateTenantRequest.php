<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('tenants.update');
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'max:20', Rule::unique('tenants', 'nik')->ignore($this->route('tenant'))],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'company' => ['nullable', 'string', 'max:150'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,former'],
        ];
    }
}
