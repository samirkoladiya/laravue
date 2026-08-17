<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitize input before validation runs.
     *
     * Defense-in-depth against stored XSS: HTML tags are stripped so a
     * `<script>`/`onerror=` payload can never reach the database, even
     * though this is an authenticated-only form - the name is rendered
     * back out in the admin header on every page.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->sanitizeSingleLine($this->input('name')),
            'email' => $this->sanitizeSingleLine($this->input('email')),
        ]);
    }

    private function sanitizeSingleLine(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim(str_replace(["\r", "\n"], ' ', strip_tags($value)));
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
            // Honeypot: a hidden field a real admin never fills. Unconstrained
            // on purpose - it must never fail validation, or a bot's
            // malformed input would surface a clue that the field is checked.
            'website' => ['nullable'],
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

    public function isSpam(): bool
    {
        return $this->filled('website');
    }
}
