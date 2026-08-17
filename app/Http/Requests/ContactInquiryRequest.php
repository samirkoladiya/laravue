<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitize input before validation runs.
     *
     * Defense-in-depth against stored XSS: HTML tags are stripped from
     * every field so a `<script>`/`onerror=` payload can never reach the
     * database, even if a future admin view were to render this data
     * without escaping it. Newlines are also stripped from the
     * single-line fields so none of them could be used for header
     * injection if this data is ever passed to Mail (e.g. as a
     * "Reply-To" built from the submitter's name/email/subject).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->sanitizeSingleLine($this->input('name')),
            'email' => $this->sanitizeSingleLine($this->input('email')),
            'subject' => $this->sanitizeSingleLine($this->input('subject')),
            'message' => $this->filled('message')
                ? trim(strip_tags((string) $this->input('message')))
                : null,
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
            'email' => ['required', 'string', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
            // Shape-only validation: a non-existent/expired session id
            // must degrade to a silently-unlinked inquiry, never fail
            // validation for a real submitter. Existence is checked (and
            // allowed to fail silently) in the controller/service layer.
            'analytics_session_id' => ['nullable', 'string', 'uuid'],
            // Honeypot: a hidden field real visitors never see or fill.
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
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'subject.required' => 'Please enter a subject.',
            'message.max' => 'Message must not exceed :max characters.',
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
