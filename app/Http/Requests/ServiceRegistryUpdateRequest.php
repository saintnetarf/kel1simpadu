<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRegistryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'max:255', 'unique:service_registry,code,' . $id],
            'base_url' => ['nullable', 'url', 'max:255'],
            'health_check_url' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'status' => ['nullable', 'string', 'in:active,inactive,maintenance'],
            'description' => ['nullable', 'string'],
        ];
    }
}
