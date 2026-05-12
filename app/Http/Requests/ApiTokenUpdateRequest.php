<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiTokenUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['sometimes','nullable','integer','exists:users,id'],
            'name' => ['sometimes','string','max:255'],
            'abilities' => ['sometimes','array'],
            'abilities.*' => ['string'],
            'rotate_token' => ['sometimes','boolean'],
        ];
    }
}
