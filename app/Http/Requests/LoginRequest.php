<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Login credentials for issuing a bearer token.
 *
 * @example {"email":"admin@poliban.ac.id","password":"password123"}
 */
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required','email'],
            'password' => ['required','string'],
        ];
    }
}
