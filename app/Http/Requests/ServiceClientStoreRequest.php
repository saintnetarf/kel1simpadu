<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceClientStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'slug' => ['required','string','max:255','unique:service_clients,slug'],
            'url' => ['nullable','url'],
            'secret' => ['nullable','string'],
            'description' => ['nullable','string'],
        ];
    }
}
