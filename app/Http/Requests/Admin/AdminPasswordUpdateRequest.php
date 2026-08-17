<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\Honeypot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminPasswordUpdateRequest extends FormRequest
{
    use Honeypot;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Laravel's built-in `current_password` rule verifies against
            // the currently authenticated guard's hashed password.
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'website' => ['nullable'], // honeypot, see Concerns\Honeypot
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Please enter your current password.',
            'current_password.current_password' => 'The current password is incorrect.',
            'password.required' => 'Please enter a new password.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
