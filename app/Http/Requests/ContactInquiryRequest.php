<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\Honeypot;
use App\Http\Requests\Concerns\SanitizesText;
use Illuminate\Foundation\Http\FormRequest;

class ContactInquiryRequest extends FormRequest
{
    use Honeypot, SanitizesText;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Newlines are stripped from the single-line fields (on top of the
     * shared HTML-stripping) so none of them could be used for header
     * injection if this data is ever passed to Mail (e.g. as a "Reply-To"
     * built from the submitter's name/email/subject).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->sanitizeSingleLine($this->input('name')),
            'email' => $this->sanitizeSingleLine($this->input('email')),
            'subject' => $this->sanitizeSingleLine($this->input('subject')),
            'message' => $this->sanitizeMultiLine($this->input('message')),
        ]);
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
            'subject.required' => 'Please enter a subject.',
            'message.max' => 'Message must not exceed :max characters.',
        ];
    }
}
