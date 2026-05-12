<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiTokenStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'user_id' => ['nullable','integer','exists:users,id'],
            'name' => ['required','string','max:255'],
            'abilities' => ['nullable','array'],
            'abilities.*' => ['string'],
        ];
    }
}
