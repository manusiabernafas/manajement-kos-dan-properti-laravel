<?php

namespace App\Http\Requests\Announcement;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('announcements.create');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'target_type' => ['required', 'in:all,property,building,floor,room'],
            'property_id' => ['nullable', 'required_if:target_type,property,building,floor,room', 'exists:properties,id'],
            'building_id' => ['nullable', 'required_if:target_type,building,floor,room', 'exists:buildings,id'],
            'floor_id' => ['nullable', 'required_if:target_type,floor,room', 'exists:floors,id'],
            'room_id' => ['nullable', 'required_if:target_type,room', 'exists:rooms,id'],
            'publish_at' => ['nullable', 'date'],
            'expired_at' => ['nullable', 'date', 'after:publish_at'],
            'status' => ['required', 'in:draft,published,archived'],
        ];
    }
}
