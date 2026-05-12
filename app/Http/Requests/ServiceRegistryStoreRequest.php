<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Payload for registering a service in Auth Center.
 *
 * @example {"name":"Academic Service","code":"academic-service","base_url":"https://api.example.com","health_check_url":"/health","contact_email":"admin@example.com","status":"active","description":"Service terdaftar"}
 */
class ServiceRegistryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:service_registry,code'],
            'base_url' => ['nullable', 'url', 'max:255'],
            'health_check_url' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'status' => ['nullable', 'string', 'in:active,inactive,maintenance'],
            'description' => ['nullable', 'string'],
        ];
    }
}
