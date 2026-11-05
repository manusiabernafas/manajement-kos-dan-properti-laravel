<?php

namespace App\Http\Requests\Building;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBuildingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('buildings.update');
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('buildings')->where('property_id', $this->property_id)->ignore($this->route('building'))],
            'description' => ['nullable', 'string'],
            'number_of_floors' => ['required', 'integer', 'min:1', 'max:100'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
