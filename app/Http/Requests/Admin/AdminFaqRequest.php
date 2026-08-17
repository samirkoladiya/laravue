<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminFaqRequest extends FormRequest
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
     * database, even if a future view were to render this data without
     * escaping it.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'question' => $this->filled('question')
                ? trim(str_replace(["\r", "\n"], ' ', strip_tags((string) $this->input('question'))))
                : null,
            'answer' => $this->filled('answer')
                ? trim(strip_tags((string) $this->input('answer')))
                : null,
            'status' => $this->boolean('status'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'status' => ['required', 'boolean'],
            // Honeypot: a hidden field real admins never fill.
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
            'question.required' => 'Please enter a question.',
            'answer.required' => 'Please enter an answer.',
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
