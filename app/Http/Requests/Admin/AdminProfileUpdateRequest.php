<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\Honeypot;
use App\Http\Requests\Concerns\SanitizesText;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminProfileUpdateRequest extends FormRequest
{
    use Honeypot, SanitizesText;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * The name is rendered back out in the admin header on every page,
     * so it's sanitized here even though this form is authenticated-only.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->sanitizeSingleLine($this->input('name')),
            'email' => $this->sanitizeSingleLine($this->input('email')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'website' => ['nullable'], // honeypot, see Concerns\Honeypot
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'photo.image' => 'The photo must be a valid image file.',
            'photo.mimes' => 'The photo must be a JPG, PNG or WEBP file.',
            'photo.max' => 'The photo must not be larger than 2MB.',
        ];
    }
}
