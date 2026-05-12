<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Payload for creating a user.
 *
 * @example {"name":"User Baru","email":"userbaru@poliban.ac.id","password":"password123","roles":[1]}
 */
class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','unique:users,email'],
            'password' => ['required','string','min:8'],
            'roles' => ['array'],
            'roles.*' => ['integer','exists:roles,id'],
        ];
    }
}
