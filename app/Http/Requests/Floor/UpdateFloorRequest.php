<?php

namespace App\Http\Requests\Floor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFloorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('floors.update');
    }

    public function rules(): array
    {
        return [
            'building_id' => ['required', 'exists:buildings,id'],
            'floor_number' => ['required', 'integer', 'min:0', 'max:200', Rule::unique('floors')->where('building_id', $this->building_id)->ignore($this->route('floor'))],
            'name' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
