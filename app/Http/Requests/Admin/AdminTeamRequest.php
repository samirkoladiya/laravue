<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\Honeypot;
use App\Http\Requests\Concerns\SanitizesText;
use Illuminate\Foundation\Http\FormRequest;

class AdminTeamRequest extends FormRequest
{
    use Honeypot, SanitizesText;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->sanitizeSingleLine($this->input('name')),
            'designation' => $this->sanitizeSingleLine($this->input('designation')),
            'email' => $this->sanitizeSingleLine($this->input('email')),
            'bio' => $this->sanitizeMultiLine($this->input('bio')),
            'facebook_url' => $this->sanitizeSingleLine($this->input('facebook_url')),
            'twitter_url' => $this->sanitizeSingleLine($this->input('twitter_url')),
            'instagram_url' => $this->sanitizeSingleLine($this->input('instagram_url')),
            'linkedin_url' => $this->sanitizeSingleLine($this->input('linkedin_url')),
            'status' => $this->boolean('status'),
        ]);
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
            'website' => ['nullable'], // honeypot, see Concerns\Honeypot
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
}
