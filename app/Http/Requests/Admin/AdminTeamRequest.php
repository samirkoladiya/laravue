<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitize input before validation runs.
     *
     * Defense-in-depth against stored XSS: HTML tags are stripped from
     * every text field so a `<script>`/`onerror=` payload can never reach
     * the database, even if a future view were to render this data
     * without escaping it.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->sanitizeSingleLine($this->input('name')),
            'designation' => $this->sanitizeSingleLine($this->input('designation')),
            'email' => $this->sanitizeSingleLine($this->input('email')),
            'bio' => $this->filled('bio')
                ? trim(strip_tags((string) $this->input('bio')))
                : null,
            'facebook_url' => $this->sanitizeSingleLine($this->input('facebook_url')),
            'twitter_url' => $this->sanitizeSingleLine($this->input('twitter_url')),
            'instagram_url' => $this->sanitizeSingleLine($this->input('instagram_url')),
            'linkedin_url' => $this->sanitizeSingleLine($this->input('linkedin_url')),
            'status' => $this->boolean('status'),
        ]);
    }

    private function sanitizeSingleLine(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return trim(str_replace(["\r", "\n"], ' ', strip_tags($value)));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Only bound on the update route (PUT/edit); null on create.
        $isCreate = $this->route('team') === null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'facebook_url' => ['nullable', 'string', 'max:255', 'regex:/^https?:\/\/.+/i'],
            'twitter_url' => ['nullable', 'string', 'max:255', 'regex:/^https?:\/\/.+/i'],
            'instagram_url' => ['nullable', 'string', 'max:255', 'regex:/^https?:\/\/.+/i'],
            'linkedin_url' => ['nullable', 'string', 'max:255', 'regex:/^https?:\/\/.+/i'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'status' => ['required', 'boolean'],
            'photo' => [
                $isCreate ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048',
            ],
            // Honeypot: a hidden field real visitors/admins never fill.
            // Unconstrained on purpose - it must never fail validation,
            // or a bot's malformed input would surface a clue that the
            // field is being checked.
            'website' => ['nullable'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter the team member\'s name.',
            'designation.required' => 'Please enter a designation.',
            'email.email' => 'Please enter a valid email address.',
            'photo.required' => 'Please upload a photo.',
            'photo.image' => 'The photo must be a valid image file.',
            'photo.mimes' => 'The photo must be a JPG, PNG or WEBP file.',
            'photo.max' => 'The photo must not be larger than 2MB.',
            'facebook_url.regex' => 'Please enter a valid URL (starting with http:// or https://).',
            'twitter_url.regex' => 'Please enter a valid URL (starting with http:// or https://).',
            'instagram_url.regex' => 'Please enter a valid URL (starting with http:// or https://).',
            'linkedin_url.regex' => 'Please enter a valid URL (starting with http:// or https://).',
        ];
    }

    /**
     * Whether this submission tripped the honeypot, i.e. is almost
     * certainly a bot. The field is invisible to real users, so any
     * value here means a scripted client filled in every input blindly.
     */
    public function isSpam(): bool
    {
        return $this->filled('website');
    }
}
