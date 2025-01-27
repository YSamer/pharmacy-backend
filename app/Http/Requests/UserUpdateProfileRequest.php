<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserUpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'phone' => ['required', 'string', 'max:20', 'unique:users,phone,' . $this->user->id],
            'name' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'email', 'unique:users,email,' . Auth::id()],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
        ];
    }
}
