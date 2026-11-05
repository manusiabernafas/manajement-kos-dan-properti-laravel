<?php

namespace App\Http\Requests\Facility;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('facilities.update');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
