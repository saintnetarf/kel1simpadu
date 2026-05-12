<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceClientUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'name' => ['sometimes','string','max:255'],
            'slug' => ['sometimes','string','max:255','unique:service_clients,slug,'.$id],
            'url' => ['nullable','url'],
            'secret' => ['nullable','string'],
            'description' => ['nullable','string'],
        ];
    }
}
